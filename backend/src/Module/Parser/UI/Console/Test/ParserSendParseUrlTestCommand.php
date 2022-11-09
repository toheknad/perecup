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
use YooKassa\Client;

#[AsCommand(
    name: 'parser:send-parse-url-test',
    description: 'Команда отправки тасков на сбор объявлений',
)]
class ParserSendParseUrlTestCommand extends Command
{
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

        $client = new Client();
        $client->setAuth('954991', 'test_fJCV-HeSHVsRH5wmSHTKKLVaUs8mOlh4DGA7YdJ6VY8');
        $payment = $client->createPayment(
            array(
                'amount' => array(
                    'value' => 350.0,
                    'currency' => 'RUB',
                ),
                'confirmation' => array(
                    'type' => 'redirect',
                    'return_url' => 'https://www.example.com/return_url',
                ),
                'capture' => true,
                'description' => 'Оплата подписки на 1 месяц',
            ),
            uniqid('', true)
        );
        print_r($payment->toArray());
    }
}
