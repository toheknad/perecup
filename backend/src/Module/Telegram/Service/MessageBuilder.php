<?php
namespace App\Module\Telegram\Service;

use App\Entity\User;
use App\Module\Parser\Entity\ParseUrl;
use App\Service\Telegram\Keyboard\Keyboard;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class MessageBuilder
{

    public static function sendWelcomeMessage(int $chatId)
    {
        $text = [];
        $text[] = "<b>🍀 Добрый день! 🍀</b>";
        $text[] = "🤖 <b>bi bi-бот</b> - поможет первым узнать о появлении новой машины!";
        $text[] = "🖥 Напишите /start для начала работы с ботом 🖥</b>";
        $text = implode(PHP_EOL, $text);


        Request::sendMessage([
            'chat_id' => $chatId,
            'text'    => $text,
            'parse_mode' => 'HTML',
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
        $text[] = '💬 <b><a href="">Инструкция по использованию бота</a></b>';
        $text[] = '📹 <b><a href=""> Видео-инструкция по использованию бота</a></b>';
        $text = implode(PHP_EOL, $text);


        Request::sendMessage([
            'chat_id' => $chatId,
            'text'    => $text,
            'parse_mode' => 'HTML',
            'reply_markup' =>  self::getKeyboard(),
        ]);
    }

    public static function sendAuthErrorMessage(int $chatId)
    {
        $text = [];
        $text[] = "<b>Для доступа к этому функционалу нужно быть полностью авторизованным</b>";
        $text = implode(PHP_EOL, $text);


        Request::sendMessage([
            'chat_id' => $chatId,
            'text'    => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    public static function sendMatchMessage(
        int $chatId,
        string $name,
        int $price,
        string $description,
        string $url,
        string $baseUrl,
        ?string $filterName,
        string $city,
        string $image,
    )
    {
//        Request::sendPhoto([
//            'chat_id' => $chatId,
//            'photo'   => $image,
//        ]);
        $priceFormated = number_format($price, 0, '.', ' ');
        $url = 'https://www.avito.ru'.$url;
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
        $text[] = '<a href="'.$image.'">Фото</a>';
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
            'text'    => $text,
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
            'text'    => $text,
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
            'text'    => $text,
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
            'text'    => $text,
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
            'text'    => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    public static function getKeyboard()
    {
        return new \Longman\TelegramBot\Entities\Keyboard(
            ["🔒 Добавить ссылку" , "📓 Мои ссылки"],
            ["💸 Подписка"],
        );
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
            'text'    => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    public static function abountSubscribe(int $chatId)
    {
        $text = [];
        $text[] = "<b>⌛️В данный момент у вас нет подписки⌛️</b>";
        $text[] = "<b>Для того, чтобы ее оформить напишите сюда, что хотите</b>";
        $text[] = "<b>оформить подписку в боте</b>";
        $text[] = "<b>Контакт: @ivan_shuga</b>";
        $text = implode(PHP_EOL, $text);


        Request::sendMessage([
            'chat_id' => $chatId,
            'text'    => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    public static function subscriptionRequired(int $chatId)
    {
        $text = [];
        $text[] = "<b>🔥 У вас нет подписки 🔥</b>";
        $text[] = "<b>Для того, чтобы ее приобрести нажмите в меню</b>";
        $text[] = "<b>💸 Подписка</b>";
        $text = implode(PHP_EOL, $text);


        Request::sendMessage([
            'chat_id' => $chatId,
            'text'    => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    public static function alreadyHasSubscription(int $chatId)
    {
        $text = [];
        $text[] = "<b>🔥 Поздравляем! 🔥</b>";
        $text[] = "<b>У вас уже есть подписка</b>";
        $text[] = "<b>Осталось дней: 10</b>";
        $text = implode(PHP_EOL, $text);


        Request::sendMessage([
            'chat_id' => $chatId,
            'text'    => $text,
            'parse_mode' => 'HTML',
        ]);
    }


}