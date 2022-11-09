<?php
namespace App\Module\Telegram\Service;

use App\Module\Parser\Entity\ParseUrl;
use App\Module\Telegram\Entity\TelegramUser;
use Doctrine\Common\Collections\Collection;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MessageBuilder
{

    public function __construct(
        protected UrlGeneratorInterface $router
    )
    {
    }

    public static function sendWelcomeMessage(int $chatId)
    {
        $text = [];
        $text[] = "<b>🍀 Добрый день! 🍀</b>";
        $text[] = "🤖 <b>bi bi-бот</b> - поможет первым узнать о появлении новой машины!";
        $text[] = "🖥 Напишите /start для начала работы с ботом 🖥</b>";
        $text = implode(PHP_EOL, $text);


        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => self::getKeyboardNotAuth(),
        ]);
    }

    public static function sendStartMessage(int $chatId)
    {
        $text = [];
        $text[] = "🚗 <b>bi bi-бот</b> — поможет узнать вам первым о появлении новой машины!";
        $text[] = "";
        $text[] = '<b>Как это работает:</b> вы добавляете ссылку-фильтр с нужными критериями для поиска авто, а затем бот мгновенно оповестит вас, когда в этом фильтре появятся новые объявления.';
        $text[] = '';
        $text[] = '<b>❗️Спешите, так как количество мест в боте ограничено ❗️</b>️';
        $text[] = "";
        $text[] = "✅ Бот поможет отслеживать появление новых объявлений на «Авито» с задержкой <b>не более 1–2 минут</b> после размещения на площадке.";
        $text[] = "";
        $text[] = "✅ Бот часто показывает объявление <b>до модерации</b> на «Авито», поэтому вы будете первым, кто его увидит.";
        $text[] = "";
        $text[] = "✅ Боты/сайты с подобным функционалом имеют <b>задержку в несколько (а иногда и в десятки) раз больше</b>, чем наш бот.";
        $text[] = "";
        $text[] = "✅ В функционале бота <b>нет ничего лишнего</b> — только то, что необходимо для успешного поиска автомобиля.";
        $text[] = "";
        $text[] = '📹 <b><a href="https://www.youtube.com/watch?v=Jz6nQDvnXUM&t=35s"> Видео-инструкция по использованию бота</a></b>';
        $text[] = '💬 <b><a href="https://trite-jackrabbit-ce3.notion.site/bibi-839950d75f7c49efaf7ef1aef1347e30">Инструкция по использованию бота</a></b>';
        $text = implode(PHP_EOL, $text);


        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => self::getKeyboardAuth(),
        ]);
    }

    public static function sendAboutTrialMode(int $chatId)
    {
        $text = [];
        $text[] = "<b>Попробуйте демо-режим бота</b>";
        $text[] = "";
        $text[] = "⚠️ Вам будет доступно <b>1 бесплатная ссылка на 2 дня</b> для того, чтобы увидеть как работает бот";
        $text[] = "";
        $text = implode(PHP_EOL, $text);

        $startTrialButton = [];
        $startTrialButton['text'] = '✅ Активировать демо-режим';
        $startTrialButton['callback_data'] = json_encode(['type' => 'trial', 'action' => 'start']);


        $keyboards = new InlineKeyboard(
            [
                $startTrialButton,
            ],
        );


        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $keyboards,
        ]);
    }

    public static function sendTrialAlreadyActivated(int $chatId)
    {
        $text = [];
        $text[] = "👋 <b>Вы уже воспользовались демо режимом!</b>";
        $text[] = "";
        $text[] = "Оформите подписку и получите весь функционал бота";

        $text = implode(PHP_EOL, $text);

//        $startTrialButton = [];
//        $startTrialButton['text'] = '💸 Подписка';
//
//
//        $keyboards = new InlineKeyboard(
//            [
//                $startTrialButton,
//            ],
//        );

//        $keyboards = [];

        // Simple digits
//        $keyboards = new Keyboard(
//            ['💸 Подписка'],
//        );

//        $keyboards->setResizeKeyboard(true);


        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
//            'reply_markup' =>  $keyboards,
        ]);
    }

    public static function sendTrialActivated(int $chatId)
    {
        $text = [];
        $text[] = "👋 <b>Отлично!</b>";
        $text[] = "";
        $text[] = "Демо режим активирован, теперь вы можете настроить бота для поиска машин";
        $text[] = "Если вы еще не знакомы с работой бота, то вот вам инструкции, обязательно посмотрите ее перед тем как начать";
        $text[] = "";
        $text[] = '📹 <b><a href="https://www.youtube.com/watch?v=Jz6nQDvnXUM&t=35s"> Видео-инструкция по использованию бота</a></b>';
        $text[] = '💬 <b><a href="https://trite-jackrabbit-ce3.notion.site/bibi-839950d75f7c49efaf7ef1aef1347e30">Инструкция по использованию бота</a></b>';
        $text[] = "";
        $text[] = "Нажмите кнопку ниже, чтобы добавить свою первую ссылку";

        $text = implode(PHP_EOL, $text);

        $startTrialButton = [];
        $startTrialButton['text'] = '🔒 Добавить ссылку';
        $startTrialButton['callback_data'] = json_encode(['type' => 'menu', 'action' => 'add-link']);


        $keyboards = new InlineKeyboard(
            [
                $startTrialButton,
            ],
        );


        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $keyboards,
        ]);
    }

    public static function sendAuthErrorMessage(int $chatId)
    {
        $text = [];
        $text[] = "<b>Для доступа к этому функционалу нужно быть полностью авторизованным</b>";
        $text = implode(PHP_EOL, $text);


        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    public static function sendMatchMessage(
        int     $chatId,
        string  $name,
        int     $price,
        string  $description,
        string  $url,
        string  $baseUrl,
        ?string $filterName,
        string  $city,
        string  $image,
    )
    {
//        Request::sendPhoto([
//            'chat_id' => $chatId,
//            'photo'   => $image,
//        ]);
        $priceFormated = number_format($price, 0, '.', ' ');
        $url = 'https://www.avito.ru' . $url;
        $text = [];
        $text[] = "<b>🚨Новое объявление🚨</b>";
        $text[] = "";
        $text[] = "🚗<b>Имя</b>: {$name}";
        $text[] = "💰<b>Цена</b>: {$priceFormated}";
        $text[] = "📖<b>Описание</b>: {$description}";
        $text[] = "🌆<b>Город</b>: {$city}";
        if ($filterName) {
            $text[] = "📁<b>Имя фильтра</b>: {$filterName}";
        }
        $text[] = '<a href="' . $image . '">Фото</a>';
        $text = implode(PHP_EOL, $text);

        $ad = [];
        $ad['text'] = 'Объявление 🚘';
        $ad['url'] = $url;

        $filter = [];
        $filter['text'] = 'Фильтр 🛠';
        $filter['url'] = $baseUrl;


        $keyboards = new InlineKeyboard(
            [
                $ad,
                $filter
            ],
        );


        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $keyboards,
        ]);

    }

    public static function sendMessageBeforeAddingLink(int $chatId)
    {
        $text = [];
        $text[] = "<b>Теперь нужно указать ссылку на фильтр</b>";
        $text[] = "<b>Пример</b>";
        $text[] = "<b><i>https://www.avito.ru/stavropol/avtomobili/s_probegom/hyundai/accent/avtomat-ASgBAQICA0SGFMjmAeC2DaKb…</i></b>";
        $text = implode(PHP_EOL, $text);


        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    public static function sendMessageAfterAddingLink(int $chatId)
    {
        $text = [];
        $text[] = "<b>Теперь укажите имя для фильтра, чтобы его проще было найти среди других</b>";
        $text = implode(PHP_EOL, $text);


        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    public static function sendMessageAfterSavingLink(int $chatId)
    {
        $text = [];
        $text[] = "<b>Фильтр успешно добавлен</b>";
        $text = implode(PHP_EOL, $text);


        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    public static function sendMessageError(int $chatId)
    {
        $text = [];
        $text[] = "<b>Что-то пошло не так</b>";
        $text = implode(PHP_EOL, $text);


        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    public static function getKeyboardAuth()
    {
        return (new \Longman\TelegramBot\Entities\Keyboard(
            ["🔒 Добавить ссылку", "📓 Мои ссылки"],
            ["💸 Подписка"],
        ))->setResizeKeyboard(true);
    }

    public static function getKeyboardNotAuth()
    {
        return (new \Longman\TelegramBot\Entities\Keyboard(
            ["💸 Начать пользоваться"],
        ))->setResizeKeyboard(true);
    }

    public static function sendAllLinksUser(int $chatId, Collection $links)
    {
        if (count($links->toArray()) === 0) {
            $text = [];
            $text[] = "<b>У вас нет ссылок</b>";
            $text = implode(PHP_EOL, $text);
            Request::sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ]);
        }

        /** @var ParseUrl $link */
        foreach ($links->toArray() as $link) {
            $text = [];
            $text[] = "<b>Id</b>: {$link->getId()}";
            $text[] = "<b>Источник</b>: {$link->getSource()}";
            $text[] = "<b>Имя фильтра</b>: {$link->getName()}";
            $text = implode(PHP_EOL, $text);

            $deleteButton = [];
            $deleteButton['text'] = 'Удалить ❌';
            $deleteButton['callback_data'] = json_encode(['type' => 'link', 'action' => 'delete', 'linkId' => $link->getId()]);

            $linkButton = [];
            $linkButton['text'] = 'Ссылка на фильтр 🛠';
            $linkButton['url'] = $link->getUrl();

            $keyboards = new InlineKeyboard(
                [
                    $deleteButton,
                    $linkButton
                ],
            );

            Request::sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
                'reply_markup' => $keyboards,
            ]);
        }
    }

    public static function sendMessageLinkDelete(int $chatId)
    {
        $text = [];
        $text[] = "<b>Ссылка успешно удалена</b>";
        $text = implode(PHP_EOL, $text);


        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    public function aboutSubscribe(TelegramUser $user)
    {
        $text = [];
        $text[] = "<b>⌛️В данный момент у вас нет подписки⌛️</b>";
        $text[] = "";
        $text[] = "<b>Какие виды подписки есть?</b>";
        $text[] = "🔥 Подписка на 1 неделю - <b>390 рублей</b>";
        $text[] = "🔥 Подписка на 2 недели - <b>590 рублей</b>";
        $text[] = "🔥 Подписка на 1 месяц - <b>890 рублей</b>";
        $text[] = "Вам будет доступно <b>5 ссылок</b> для отслеживания новых машин";
        $text = implode(PHP_EOL, $text);

        $oneWeek = [];
        $oneWeek['text'] = 'Купить 1 неделю';
        $oneWeek['url'] = $_ENV['BASE_URL'] . $this->router->generate('payment_create', [
                'userId' => $user->getId(),
                'subscriptionType' => 2
            ]);

        $twoWeek = [];
        $twoWeek['text'] = 'Купить 2 недели';
        $twoWeek['url'] = $_ENV['BASE_URL'] . $this->router->generate('payment_create', [
                'userId' => $user->getId(),
                'subscriptionType' => 3
            ]);

        $oneMonth = [];
        $oneMonth['text'] = 'Купить 1 месяц';
        $oneMonth['url'] = $_ENV['BASE_URL'] . $this->router->generate('payment_create', [
                'userId' => $user->getId(),
                'subscriptionType' => 4
            ]);


        $keyboards = new InlineKeyboard(
            [
                $oneWeek,
                $twoWeek,
                $oneMonth
            ],
        );


        Request::sendMessage([
            'chat_id' => $user->getChatId(),
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $keyboards
        ]);
    }

    public function alreadyHasSubscription(TelegramUser $user)
    {
        $text = [];
        $keyboards = [];
        if ($user->hasUserTrial()) {
            $text[] = "<b>⌛️У вас демо-режим ⌛️</b>";
        }
        if ($user->hasUserStandart()) {
            $text[] = "<b>⌛️У вас стандартная подписка⌛️</b>";
        }
        $text[] = '';
        $interval = (new \DateTimeImmutable())->diff($user->getSubscribe()->last()->getActivatedTo());
        $amountDays = $interval->format('%a');
        if ((int)$amountDays === 0) {
            $text[] = "Сегодня последний день подписки";
        } else {
            $text[] = "Осталось дней: <b>{$amountDays}</b>";
        }

        if ((int)$amountDays === 0 || $user->hasUserTrial()) {
            $text[] = '';
            $text[] = "<b>Хотите продлить подписку?</b>";
            $text[] = "🔥 Подписка на 1 неделю - <b>390 рублей</b>";
            $text[] = "🔥 Подписка на 2 недели - <b>590 рублей</b>";
            $text[] = "🔥 Подписка на 1 месяц - <b>890 рублей</b>";
            $text[] = "Вам будет доступно <b>5 ссылок</b> для отслеживания новых машин";

            $oneWeek = [];
            $oneWeek['text'] = 'Купить 1 неделю';
            $oneWeek['url'] = $_ENV['BASE_URL'] . $this->router->generate('payment_create', [
                    'userId' => $user->getId(),
                    'subscriptionType' => 2
                ]);

            $twoWeek = [];
            $twoWeek['text'] = 'Купить 2 недели';
            $twoWeek['url'] = $_ENV['BASE_URL'] . $this->router->generate('payment_create', [
                    'userId' => $user->getId(),
                    'subscriptionType' => 3
                ]);

            $oneMonth = [];
            $oneMonth['text'] = 'Купить 1 месяц';
            $oneMonth['url'] = $_ENV['BASE_URL'] . $this->router->generate('payment_create', [
                    'userId' => $user->getId(),
                    'subscriptionType' => 4
                ]);


            $keyboards = new InlineKeyboard(
                [
                    $oneWeek,
                    $twoWeek,
                    $oneMonth
                ],
            );
        }

        $text = implode(PHP_EOL, $text);

        $response = [
            'chat_id' => $user->getChatId(),
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if (!empty($keyboards)) {
            $response['reply_markup'] = $keyboards;
        }
        Request::sendMessage($response);
    }

    public static function maxAmountLinks(int $chatId)
    {
        $text = [];
        $text[] = "👋 <b>У вас лимит по ссылкам!</b>";
        $text[] = "";
        $text[] = "Если у вас демо режим, то купите подписку, чтобы иметь больше ссылок";

        $text = implode(PHP_EOL, $text);

        $startTrialButton = [];
        $startTrialButton['text'] = '💸 Подписка';
        $startTrialButton['callback_data'] = json_encode(['type' => 'menu', 'action' => 'subscription']);


        $keyboards = new InlineKeyboard(
            [
                $startTrialButton,
            ],
        );


        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $keyboards,
        ]);
    }

    public static function wrongLink(int $chatId)
    {
        $text = [];
        $text[] = "👋 <b>Неправильная ссылка!</b>";
        $text[] = "";
        $text[] = "Вы уверены, что это ссылка из авито?";
        $text[] = "Попробуйте еще раз";

        $startTrialButton = [];
        $startTrialButton['text'] = '🔒 Добавить ссылку';
        $startTrialButton['callback_data'] = json_encode(['type' => 'menu', 'action' => 'add-link']);


        $keyboards = new InlineKeyboard(
            [
                $startTrialButton,
            ],
        );

        $text = implode(PHP_EOL, $text);


        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $keyboards
        ]);
    }

    public static function subscriptionActivated(TelegramUser $telegramUser, int $subscriptionType)
    {
        $text = [];
        $text[] = "👋 <b>Отлично!</b>";
        $text[] = "";
        if ($subscriptionType == 2) {
            $text[] = "Вы купили подписку на 1 неделю";
        } elseif ($subscriptionType == 3) {
            $text[] = "Вы купили подписку на 2 недели";
        } elseif ($subscriptionType == 4) {
            $text[] = "Вы купили подписку на 1 месяц";
        }
        $text[] = "Нажмите кнопку ниже, если хотите добавить новую ссылку";

        $text = implode(PHP_EOL, $text);

        $startTrialButton = [];
        $startTrialButton['text'] = '🔒 Добавить ссылку';
        $startTrialButton['callback_data'] = json_encode(['type' => 'menu', 'action' => 'add-link']);


        $keyboards = new InlineKeyboard(
            [
                $startTrialButton,
            ],
        );


        Request::sendMessage([
            'chat_id' => $telegramUser->getChatId(),
            'text'    => $text,
            'parse_mode' => 'HTML',
            'reply_markup' =>  $keyboards,
        ]);
    }


}