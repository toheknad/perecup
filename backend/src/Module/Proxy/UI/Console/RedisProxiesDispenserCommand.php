<?php

namespace App\Module\Proxy\UI\Console;

use App\Module\AddressNormalizer\Service\AvitoDriver;
use App\Module\AddressNormalizer\Service\StreetNormalizer;
use App\Module\Parser\Message\ParseUrlMessage;
use App\Module\Parser\Repository\ParseUrlRepository;
use App\Module\Proxy\Entity\Proxy;
use App\Module\Proxy\Repository\ProxyRepository;
use App\Module\UrlChecked\Repository\UrlCheckedRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'proxy:dispenser',
    description: 'Управление прокси для тасков',
)]
class RedisProxiesDispenserCommand extends Command
{
    private const REDIS_FREE_PROXIES = 'free_proxies';
    private const REDIS_HOLD_PROXIES = 'hold_proxies';

    private $client;
    private int $lastUrlId;

    private int $lastTimeUpdateProxiesArray;
    private array $activeProxies = [];

    public function __construct(
//        private EntityManagerInterface $entityManager,
        private ProxyRepository $proxyRepository,
        private ParseUrlRepository  $parseUrlRepository,
        private UrlCheckedRepository  $urlCheckedRepository,
        private MessageBusInterface $messageBus,
        string $name = null
    )
    {
        $this->client = RedisAdapter::createConnection(
            'redis://redis:6379'
        );
        parent::__construct($name);
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** Добавляем прокси из БД в free_proxies */
        $this->setProxiesIntoRedis($io);

        /** Сохраняем список прокси, чтобы отслеживать появление новых прокси в бд в дальнейшем */
        $this->updateProxiesArray($io);

        /** Нужен, чтобы знать с какой ссылки брать следующие  */
        $this->lastUrlId = 0;

        while(true) {
            if ($this->client->llen(self::REDIS_FREE_PROXIES)) {
                // Если в free_proxies что-то есть, то мы создаем таски
                $this->createTaskFromFreeProxies($io);
            } elseif ($amountHoldProxies = $this->client->llen(self::REDIS_HOLD_PROXIES)){
                // если в free_proxies ничего нет, то мы идем в hold_proxies
                $this->transerProxiesFromHoldToFree($io, $amountHoldProxies);
            }
            sleep(1);
        }
    }

    /**
     * @throws \JsonException
     */
    private function transerProxiesFromHoldToFree(SymfonyStyle $io, int $amountHoldProxies)
    {
        $this->updateProxiesArray($io);
        $time = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $amountReadyProxies = 0;
        /** @var Proxy $proxy */
        for ($i = 0; $i < $amountHoldProxies; $i++) {
            $freeProxy = $this->client->lpop(self::REDIS_HOLD_PROXIES);
            if (!$freeProxy) {
                sleep(1);
                return;
            }
            $freeProxy = json_decode($freeProxy, true);

            $now = new DateTime();
            $holdPassTime = (new DateTime($freeProxy['lastUsingTime']))->modify("+ {$freeProxy['holdSeconds']} seconds");
            if ($now >= $holdPassTime) {
                $this->client->lpush(self::REDIS_FREE_PROXIES, json_encode($freeProxy, JSON_THROW_ON_ERROR));
                $amountReadyProxies++;
                $io->success($time . ' - Прокси отправлено в free_proxies!');
            } else {
                $this->client->lpush(self::REDIS_HOLD_PROXIES, json_encode($freeProxy, JSON_THROW_ON_ERROR));
            }
        }

        // если не было ни одного готового прокси, то нужно подождать секунду, чтобы редис не выполнял слишком много запросов
        if ($amountReadyProxies === 0) {
            sleep(1);
        }
    }

    private function setProxiesIntoRedis(SymfonyStyle $io)
    {
        $proxiesFromDb = $this->proxyRepository->findAll();
        $time = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        /** @var Proxy $proxy */
        foreach ($proxiesFromDb as $proxy) {
            $proxyData = [
                'data' => $proxy->getArray(),
                'amountLinks' => 10, // Каждый прокси сейчас парсит 10 ссылок раз в минуту + 1 минут остывания,
                'holdSeconds' => 60
            ];
            $this->client->lpush(self::REDIS_FREE_PROXIES, json_encode($proxyData, JSON_THROW_ON_ERROR));
            $io->success($time . " - Прокси добавленно!");
        }
    }

    private function createTaskFromFreeProxies(SymfonyStyle $io)
    {
        $freeProxy = $this->client->lpop(self::REDIS_FREE_PROXIES);
        if (!$freeProxy) {
            return;
        }
        $freeProxy = json_decode($freeProxy, true);
        $linksEntities = $this->parseUrlRepository->getUrlsForProxy($this->lastUrlId, $freeProxy['amountLinks']);
        $amountLinks = $this->parseUrlRepository->count([]);
        $preparedLinks = [];
        foreach ($linksEntities as $link) {
            $isFirstCheck = false;
            if ($this->urlCheckedRepository->count(['parseUrl' => $link]) === 0) {
                echo $link->getId() . " FIRST " . PHP_EOL;
                $isFirstCheck = true;
            } else {
                echo $link->getId() . " NOT FIRST " . PHP_EOL;
            }
            $preparedLinks[] = [
                'id' => $link->getId(),
                'source' => $link->getSource(),
                'url' => $link->getUrl(),
                'userId' => $link->getUser()->getId(),
                'isFirstCheck' => $isFirstCheck,
                'sleepSeconds' => 3,
            ];
            // если взялось ссылок меньше, чем $amountLinks значит в этот раз взялись послдении ссылки
            // и нужно обнулить счетчик, чтобы брать записи с самого начала.
            $this->lastUrlId++;
            if ($this->lastUrlId === $amountLinks) {
                $this->lastUrlId = 0;
            }
        }
        $parseUrlMessage = new ParseUrlMessage('avito', $preparedLinks, $freeProxy);
        $this->messageBus->dispatch($parseUrlMessage);
        $io->success('Таск отправлен!');
    }

    private function updateProxiesArray(SymfonyStyle $io)
    {
        if (empty($this->activeProxies)) {
            $proxiesFromDb = $this->proxyRepository->findAll();
            /** @var Proxy $proxy */
            foreach ($proxiesFromDb as $proxy) {
                $this->activeProxies[$proxy->getProxy()] = true;
            }
            $this->lastTimeUpdateProxiesArray = time();
            $io->success(" Загружен список прокси локально!");
        }

        if (time() >= $this->lastTimeUpdateProxiesArray + 60) {
            $proxiesFromDb = $this->proxyRepository->findAll();
            $newActiveProxies = [];
            foreach ($proxiesFromDb as $proxy) {
                $newActiveProxies[$proxy->getProxy()] = true;
                if (!isset($this->activeProxies[$proxy->getProxy()])) {
                    $proxyData = [
                        'data' => $proxy->getArray(),
                        'amountLinks' => 10, // Каждый прокси сейчас парсит 10 ссылок раз в минуту + 1 минут остывания,
                        'holdSeconds' => 60
                    ];
                    $this->client->lpush(self::REDIS_FREE_PROXIES, json_encode($proxyData, JSON_THROW_ON_ERROR));
                    $io->success(" Обнаружен новый прокси в БД!");
                }
            }
            $this->activeProxies = $newActiveProxies;
            $this->lastTimeUpdateProxiesArray = time();
        }
    }
}
