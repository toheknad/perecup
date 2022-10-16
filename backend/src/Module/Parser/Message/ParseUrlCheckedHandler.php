<?php

namespace App\Module\Parser\Message;

use App\Module\Parser\Repository\ParseUrlRepository;
use App\Module\Telegram\Entity\TelegramUser;
use App\Module\Telegram\Repository\TelegramUserRepository;
use App\Module\Telegram\Service\MessageBuilder;
use App\Module\Telegram\Service\TelegramClient;
use App\Module\UrlChecked\Entity\UrlChecked;
use App\Module\UrlChecked\Repository\UrlCheckedRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Longman\TelegramBot\Telegram;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Longman\TelegramBot\Request;

#[AsMessageHandler]
class ParseUrlCheckedHandler
{
    private Telegram $telegram;

    public function __construct(
        private ParseUrlRepository $parseUrlRepository,
        private UrlCheckedRepository $urlCheckedRepository,
        private EntityManagerInterface $entityManager,
        private TelegramUserRepository $telegramUserRepository,
        TelegramClient $telegram,
    )
    {
        $this->telegram = $telegram->getClient();
    }

    public function __invoke(ParseUrlCheckedMessage $message)
    {

        $dt = (new DateTime())->format('Y-m-d H:i:s');
        if ($this->urlCheckedRepository->findOneBy(['url' => $message->url, 'telegramUser' => $message->userId])) {
            echo $dt.' - Url has already exists' . PHP_EOL;
            return;
        }
        if (!$parseUrl = $this->parseUrlRepository->findOneBy(['url' => $message->baseUrl, 'user' => $message->userId])) {
            echo $dt.' - Url is not found' . PHP_EOL;
            return;
        }

        /** @var TelegramUser $telegramUser */
        $telegramUser = $this->telegramUserRepository->find($message->userId);
        $urlChecked = new UrlChecked();
        $urlChecked->setUrl($message->url);
        $urlChecked->setParseUrl($parseUrl);
        $urlChecked->setUser($telegramUser);
        $this->entityManager->persist($urlChecked);
        $this->entityManager->flush();

        MessageBuilder::sendMatchMessage(
            $telegramUser->getChatId(),
            $message->name,
            $message->price,
            $message->description,
            $message->url,
            $message->baseUrl,
        );

        echo $dt. '- Message send to 588866042' . PHP_EOL;

    }
}