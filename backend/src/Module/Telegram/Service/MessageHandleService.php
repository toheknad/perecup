<?php

namespace App\Module\Telegram\Service;

use App\Module\Parser\Entity\ParseUrl;
use App\Module\Telegram\Entity\TelegramUser;
use App\Module\Telegram\Repository\TelegramUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Longman\TelegramBot\Request;

class MessageHandleService
{

    private ?TelegramUser $user;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected TelegramUserRepository $telegramUserRepository,
    )
    {
    }

    /**
     * @param array $message
     */
    public function start(array $message)
    {
//        echo "<pre>";
//        print_r($message);
//        echo "</pre>";
        if (isset($message['message'])) {
            $this->user = $this->getUserByChatId($message['message']['from']['id']);
        }
        $this->handleMessageByType($message);

    }

    /**
     * @param array $message
     */
    private function handleMessageByType(array $message)
    {
        // если юзер еще не заполнил профиль
        if ($this->isFirstMessageFromUser($message) && $message['message']['text'][0] !== '/') {
            MessageBuilder::sendWelcomeMessage($message['message']['from']['id']);
            return;
        }

        try {

            if (isset($message['message']['text']) && $message['message']['text'] === '/start') {
                MessageBuilder::sendStartMessage($message['message']['from']['id']);
            } elseif (isset($message['callback_query'])) {
//                $this->callbackQueryHandler->process($message);
            } elseif (isset($message['message']['text']) && $message['message']['text'] === '🔒 Добавить ссылку') {
                $this->user->setAction(ActionList::ADDING_LINK);
                $this->entityManager->persist($this->user);
                $this->entityManager->flush();
                MessageBuilder::sendMessageBeforeAddingLink($message['message']['from']['id']);
            } elseif (isset($message['message']['text']) && $message['message']['text'] === '📓 Мои ссылки') {
                MessageBuilder::sendAllLinksUser($message['message']['from']['id'], $this->user->getParseUrls());
            } else {
                $this->actionHandler($message);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    private function isFirstMessageFromUser(array $message)
    {

        $telegramUserId = $message['message']['from']['id'];
        /** @var TelegramUser $user */
        if (!$this->telegramUserRepository->findOneBy(['chatId' => $telegramUserId])) {
            $user = new TelegramUser();
            $user->setChatId($telegramUserId);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return true;
        }

        return false;
    }

    private function getUserByChatId(int $chatId): ?TelegramUser
    {
        if ($user = $this->telegramUserRepository->findOneBy(['chatId' => $chatId])) {
            return $user;
        }
        return null;
    }

    private function actionHandler(array $message)
    {
        if ($this->user->getAction() === ActionList::ADDING_LINK) {
                $parseUrl = new ParseUrl();
                $parseUrl->setUrl($message['message']['text']);
                $parseUrl->setPeriod(1);
                $parseUrl->setSource('avito');
                $parseUrl->setUser($this->user);

                $this->user->setAction(0);

                $this->entityManager->persist($parseUrl);
                $this->entityManager->flush();
                MessageBuilder::sendMessageAfterAddingLink($message['message']['from']['id']);

        }
    }
}