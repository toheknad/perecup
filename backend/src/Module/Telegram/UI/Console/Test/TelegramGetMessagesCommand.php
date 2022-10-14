<?php

namespace App\Module\Telegram\UI\Console\Test;

use App\Module\Parser\Message\ParseUrlCheckedMessage;
use App\Module\Parser\Message\ParseUrlMessage;
use App\Module\Parser\Repository\ParseUrlRepository;
use App\Module\Proxy\Repository\ProxyRepository;
use App\Module\Telegram\Service\MessageHandleService;
use App\Module\Telegram\Service\TelegramClient;
use DateTime;
use Longman\TelegramBot\Telegram;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[AsCommand(
    name: 'telegram:get-messages',
    description: 'Команда отправки тасков на сбор объявлений',
)]
class TelegramGetMessagesCommand extends Command
{
    private Telegram $telegram;
    private MessageHandleService $messageHandleService;

    public function __construct(
        TelegramClient $telegram,
        MessageHandleService $messageHandleService,
        string $name = null
    )
    {
        $this->telegram = $telegram->getClient();
        $this->messageHandleService = $messageHandleService;
        parent::__construct($name);
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $messages = $this->telegram->handleGetUpdates()->getRawData();
        foreach ($messages['result'] as $message) {
            $this->messageHandleService->start($message);
        }
        return Command::SUCCESS;
    }
}
