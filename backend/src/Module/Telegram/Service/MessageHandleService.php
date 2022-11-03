<?php

namespace App\Module\Telegram\Service;

use App\Module\Parser\Entity\ParseUrl;
use App\Module\Parser\Repository\ParseUrlRepository;
use App\Module\Subscribe\Entity\Subscribe;
use App\Module\Subscribe\Repository\SubscribeRepository;
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
        protected ParseUrlRepository $parseUrlRepository,
        protected SubscribeRepository $subscribeRepository,
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
        $this->entityManager->clear();
        if (isset($message['message']) || isset($message['callback_query'])) {
            if (isset($message['message']['from']['id'])) {
                $telegramUserId = $message['message']['from']['id'];
            } elseif (isset($message['callback_query']['from']['id'])) {
                $telegramUserId = $message['callback_query']['from']['id'];
            }
            $this->user = $this->getUserByChatId($telegramUserId);
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
                if (!$this->user->getSubscribe()) {
                    MessageBuilder::sendAboutTrialMode($message['message']['from']['id']);
                }
            } elseif (isset($message['message']['text']) && $message['message']['text'] === '🔒 Добавить ссылку') {
                $this->menuButtonAddLink();
            } elseif (isset($message['message']['text']) && $message['message']['text'] === '📓 Мои ссылки') {
                if (!$this->user->isUserHasSubscribe()) {
                    MessageBuilder::abountSubscribe($message['message']['from']['id']);
                    return;
                }
                $this->user->setAction(0);
                $this->entityManager->persist($this->user);
                $this->entityManager->flush();
                MessageBuilder::sendAllLinksUser($message['message']['from']['id'], $this->user->getParseUrls());
            } elseif (isset($message['message']['text']) && $message['message']['text'] === '💸 Подписка') {
                $this->menuSubscription();
            } elseif (isset($message['callback_query'])) { // нажатие на кнопку
                $this->callbackHandler($message);
            } else {
                $this->actionHandler($message);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    private function isFirstMessageFromUser(array $message)
    {

        if (isset($message['message']['from']['id'])) {
            $telegramUserId = $message['message']['from']['id'];
        } elseif (isset($message['callback_query']['from']['id'])) {
            $telegramUserId = $message['callback_query']['from']['id'];
        }
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
                $url = $message['message']['text'];

                if (!mb_stripos($url, 'avito.ru')) {
                    $this->user->setAction(0);
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                    MessageBuilder::wrongLink($message['message']['from']['id']);
                    return;
                }
                // чтобы была галочка "сначала в выбранном радиусе"
                if (mb_stripos($url, '&localPriority=0')) {
                    $url = str_replace('&localPriority=0', '&localPriority=1', $url);
                }
                if (!mb_stripos($url, '&localPriority=1')) {
                    $url .= '&localPriority=1';
                }

                // если ссылка мобильная
                $url = str_replace('m.avito', 'avito', $url);
                $parseUrl = new ParseUrl();
                $parseUrl->setUrl($url);
                $parseUrl->setPeriod(1);
                $parseUrl->setSource('avito');
                $parseUrl->setIsActive(false);
                $parseUrl->setUser($this->user);

                $this->user->setAction(ActionList::ADDING_LINK_NAME);

                $this->entityManager->persist($parseUrl);
                $this->entityManager->flush();
                $this->entityManager->clear();
                MessageBuilder::sendMessageAfterAddingLink($message['message']['from']['id']);

        } elseif ($this->user->getAction() === ActionList::ADDING_LINK_NAME) {
            if ($parseUrl = $this->parseUrlRepository->findOneBy(['user' => $this->user->getId()], ['id' => 'DESC'])) {
                $parseUrl->setName($message['message']['text']);
                $parseUrl->setIsActive(true);
                $this->user->setAction(0);
                $this->entityManager->flush();
                $this->entityManager->clear();
                MessageBuilder::sendMessageAfterSavingLink($message['message']['from']['id']);
            } else {
                $this->user->setAction(0);
                MessageBuilder::sendMessageError($message['message']['from']['id']);
            }
        }
    }

    /**
     * @throws \Exception
     */
    private function callbackHandler(array $message)
    {
        print_r($message);
        if (!isset($message['callback_query']['data'])) {
            throw new \Exception('callback error');
        }
        $data = json_decode($message['callback_query']['data'], true);

        if ($data['type'] === 'link') {
            if ($data['action'] === 'delete') {
                $parseUrl = $this->parseUrlRepository->find($data['linkId']);
                if ($parseUrl) {
                    $this->entityManager->remove($parseUrl);
                    $this->entityManager->flush();
                    MessageBuilder::sendMessageLinkDelete($this->user->getChatId());
                }
            }
        }

        if ($data['type'] === 'trial') {
            if ($data['action'] === 'start') {
                // если еще ни разу не было подписки у юзера
                if (!$this->subscribeRepository->findOneBy(['telegramUser' => $this->user])) {
                    $subscribe = new Subscribe();
                    $subscribe->setTelegramUser($this->user);
                    $subscribe->setTrial();

                    $this->user->setSubscribe($subscribe);
                    $this->entityManager->persist($this->user);
                    $this->entityManager->flush();
                    MessageBuilder::sendTrialActivated($this->user->getChatId());
                    return;
                }
                MessageBuilder::sendTrialAlreadyActivated($this->user->getChatId());
            }
        }

        if ($data['type'] === 'menu') {
            if ($data['action'] === 'add-link') {
                $this->menuButtonAddLink();
            }
            if ($data['action'] === 'subscription') {
                $this->menuSubscription();
            }
        }
    }

    private function menuButtonAddLink()
    {
        if (!$this->user->isUserHasSubscribe()) {
            MessageBuilder::abountSubscribe($this->user->getChatId());
            return;
        }
        if ($this->user->getMaxAmountLinks() > $this->user->getAmountLinks()) {
            $this->user->setAction(ActionList::ADDING_LINK);
            $this->entityManager->persist($this->user);
            $this->entityManager->flush();
            MessageBuilder::sendMessageBeforeAddingLink($this->user->getChatId());
        } else {
            MessageBuilder::maxAmountLinks($this->user->getChatId());
        }
    }

    private function menuSubscription()
    {
        $this->user->setAction(0);
        $this->entityManager->persist($this->user);
        $this->entityManager->flush();
        if ($this->user->isUserHasSubscribe()) {
            MessageBuilder::alreadyHasSubscription($this->user);
        } else {
            MessageBuilder::abountSubscribe($this->user->getChatId());
        }
    }
}