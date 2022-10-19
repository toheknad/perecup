<?php

namespace App\Module\Parser\UI\Console\Test;

use App\Module\Parser\Message\ParseUrlCheckedMessage;
use App\Module\Parser\Message\ParseUrlMessage;
use App\Module\Parser\Repository\ParseUrlRepository;
use App\Module\Proxy\Repository\ProxyRepository;
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
    name: 'parser:send-parse-url',
    description: 'Команда отправки тасков на сбор объявлений',
)]
class ParserSendParseUrlCommand extends Command
{
    public function __construct(
        private ParseUrlRepository  $parseUrlRepository,
        private MessageBusInterface $messageBus,
        private LoggerInterface     $logger,
        private ProxyRepository     $proxyRepository,
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
        $ts = microtime(true);
        $this->logger->info('Запущено!');
        $io = new SymfonyStyle($input, $output);

        while(true) {
//            $proxy = $this->proxyRepository->getNextProxy();
            $lstParseUrl = $this->parseUrlRepository->getActiveNow();
            $dt = (new DateTime())->format('Y-m-d H:i:s');
            echo $dt . " - START". PHP_EOL;
            sleep(50);
            foreach ($lstParseUrl as $item) {
                echo $dt . " - LINK ADD". PHP_EOL;
                $parseUrlMessage = ParseUrlMessage::createFromEntity($item, '2323:323');
                $en = $this->messageBus->dispatch($parseUrlMessage);
                $this->logger->debug('Отправлено в очередь!', [$en]);
            }
            $this->logger->info('Успешно заврешено, [время выполнения]', [microtime(true) - $ts]);
        }


        return Command::SUCCESS;
    }
}
