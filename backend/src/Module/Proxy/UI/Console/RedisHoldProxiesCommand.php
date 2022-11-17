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
    name: 'proxy:hold-proxies',
    description: 'Управление прокси для тасков',
)]
class RedisHoldProxiesCommand extends Command
{
    private const REDIS_FREE_PROXIES = 'free_proxies';
    private const REDIS_HOLD_PROXIES = 'hold_proxies';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProxyRepository $proxyRepository,
        private ParseUrlRepository  $parseUrlRepository,
        private UrlCheckedRepository  $urlCheckedRepository,
        private MessageBusInterface $messageBus,
        string $name = null
    )
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $client = RedisAdapter::createConnection(
            'redis://redis:6379'
        );
        $time = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        /** @var Proxy $proxy */
        while(true) {
            $freeProxy = $client->lpop(self::REDIS_HOLD_PROXIES);
            if (!$freeProxy) {
                sleep(2);
                continue;
            }
            $freeProxy = json_decode($freeProxy, true);
            print_r($freeProxy);
            $proxyReady = false;
            $sec = 0;
            while(!$proxyReady) {
                $now = new DateTime();
                $holdPassTime = (new DateTime($freeProxy['lastUsingTime']))->modify("+ {$freeProxy['holdSeconds']} seconds");
                if ($now >= $holdPassTime) {
                    $proxyReady = true;
                } else {
                    sleep(1);
                }
//                $sec++;
//                echo PHP_EOL . $sec . PHP_EOL;
            }

            $client->lpush(self::REDIS_FREE_PROXIES, json_encode($freeProxy, JSON_THROW_ON_ERROR));
            $io->success('Прокси отправлено в очередь!');
        }
    }
}
