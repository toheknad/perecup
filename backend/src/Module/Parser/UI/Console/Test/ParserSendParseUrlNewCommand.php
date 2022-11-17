<?php

namespace App\Module\Parser\UI\Console\Test;

use App\Module\Parser\Message\ParseUrlCheckedMessage;
use App\Module\Parser\Message\ParseUrlMessage;
use App\Module\Parser\Repository\ParseUrlRepository;
use App\Module\Proxy\Repository\ProxyRepository;
use App\Module\UrlChecked\Repository\UrlCheckedRepository;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'parser:send-url',
    description: 'Команда отправки тасков на сбор объявлений',
)]
class ParserSendParseUrlNewCommand extends Command
{
    private const MAX_AMOUNT_REQUEST_FOR_ONE_PROXY = 20;
    private const MAX_PARSING_TIME = 1.5;

    public function __construct(
        private ParseUrlRepository  $parseUrlRepository,
        private MessageBusInterface $messageBus,
        private LoggerInterface     $logger,
        private ProxyRepository     $proxyRepository,
        private UrlCheckedRepository $urlCheckedRepository,
        string                      $name = null
    )
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        while(true) {
            $io = new SymfonyStyle($input, $output);
            $dt = (new DateTime())->format('Y-m-d H:i:s');
            $io->success(sprintf('"%s" - Подготовка ссылки для отправки на парсинг', $dt));

            $links = $this->parseUrlRepository->getActiveNow();
            $maxAmountUrlForProxy = 0;
            $amountLinks = count($links);
            $amountProxies = $this->proxyRepository->count([]);

            if ($amountLinks !== 0 && $amountProxies !== 0) {
                $maxParsingTime = $amountLinks / (($amountProxies / 2 ) * self::MAX_AMOUNT_REQUEST_FOR_ONE_PROXY);
                $io->info(sprintf('"%s" - Максимальное время для парсинга = "%s" сек', $dt, $maxParsingTime * 10 * 6));
            } else {
                $io->error(
                    sprintf('"%s" - Проблема с вычислением времени для парсинга (проверить ссылки или прокси)', $dt)
                );
                return 0;
            }

            if($maxParsingTime > self::MAX_PARSING_TIME) {
                $io->error(
                    sprintf('"%s" - Время для парсинга превышает максимальные лимиты', $dt)
                );
            }
            $proxy = $this->proxyRepository->getNextProxy();
            foreach ($links as $item) {
//                if ($maxAmountUrlForProxy === self::MAX_AMOUNT_REQUEST_FOR_ONE_PROXY) {
//                    $proxy = $this->proxyRepository->getNextProxy();
//                    $maxAmountUrlForProxy = 0;
//                }
                if ($this->urlCheckedRepository->count(['parseUrl' => $item]) === 0) {
                    $parseUrlMessage = ParseUrlMessage::createFromEntity($item,  true);
                } else {
                    $parseUrlMessage = ParseUrlMessage::createFromEntity($item,false);
                }
                $this->messageBus->dispatch($parseUrlMessage);
                $maxAmountUrlForProxy++;
                $io->info(sprintf('"%s" - Ссылка отправлена ', $dt));
                $io->info(sprintf('"%s" - Прокси - "%s"', $dt, $proxy->getProxy()));
            }
//            sleep($maxParsingTime * 10 * 6);
            sleep(120);
        }
    }
}
