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
    public static function sendResultBySearchToUser(User $userBySearch, int $chatId)
    {
        $text = [];
        $text[] = "<b>{$userBySearch->getName()} {$userBySearch->getSurname()}</b>";
        $text[] = "<b><i>Возраст</i></b>: {$userBySearch->getAge()}";
        $text[] = "<b><i>Пол</i></b>: {$userBySearch->getGender()}";
        $text[] = "<b><i>Город</i></b>: {$userBySearch->getCity()}";
        $text[] = "<b><i>Описание</i></b>: {$userBySearch->getAbout()}";
        $text = implode(PHP_EOL, $text);

        Request::sendPhoto([
            'chat_id' => $chatId,
            'photo'  => $userBySearch->getPhoto()
        ]);

        $likeButton = [];
        $likeButton['text'] = '👎';
        $likeButton['callback_data'] = json_encode(['type' => 'search', 'action' => 'dislike', 'userId' => $userBySearch->getId()]);

        $dislikeButton = [];
        $dislikeButton['text'] = '👍️';
        $dislikeButton['callback_data'] = json_encode(['type' => 'search', 'action' => 'like', 'userId' => $userBySearch->getId()]);

        $keyboards = new InlineKeyboard(
            [
                $likeButton,
                $dislikeButton
            ],
        );

        Request::sendMessage([
            'chat_id' => $chatId,
            'text'    => $text,
            'parse_mode' => 'HTML',
            'reply_markup' =>  $keyboards,
        ]);
    }

    public static function sendNotFoundBySearch(int $chatId)
    {
        $text = [];
        $text[] = "<b>К сожалению не удалось найти новых анкет</b>";
        $text = implode(PHP_EOL, $text);


        Request::sendMessage([
            'chat_id' => $chatId,
            'text'    => $text,
            'parse_mode' => 'HTML',
        ]);
    }


    public static function sendWelcomeMessage(int $chatId)
    {
        $text = [];
        $text[] = "<b>Добрый день!</b>";
        $text[] = "<b>Видимо, у вас все еще нет профиля</b>";
        $text[] = "<b>Напишите /start чтобы начать</b>";
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
        $text[] = "<b>Добро пожаловать!</b>";
        $text[] = "<b>Я помогу вам знать первым о всех новых объявлениях</b>";
        $text[] = "<b>Выберите в меню, что вы хотите сделать</b>";
        $text[] = "<b>{{$chatId}}</b>";
//        $text[] = "<b>И в следующем сообщении укажи ссылку из авито</b>";
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

    public static function sendMatchMessage(int $chatId, string $name, int $price, string $description, string $url)
    {
        $url = 'https://www.avito.ru'.$url;
        $text = [];
        $text[] = "<b>Ванечек, новый улов</b>";
        $text[] = "<b></b>";
        $text[] = "<b><i>Имя</i></b>: {$name}";
        $text[] = "<b><i>Цена</i></b>: {$price}";
        $text[] = "<b><i>Описание</i></b>: {$description}";
        $text[] = "<b><i>Сссылка:</i></b>: {$url}";
        $text = implode(PHP_EOL, $text);


        Request::sendMessage([
            'chat_id' => $chatId,
            'text'    => $text,
            'parse_mode' => 'HTML',
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
        $text[] = "<b>Фильтр успешно добавлен</b>";
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
//            ["📓 Руководство", "📖 О сервисе	"],
        );
    }

    public static function sendAllLinksUser(int $chatId, Collection $links)
    {
        foreach ($links->toArray() as $link) {
            $text = [];
            $text[] = "<b><i>Id:</i></b>: {$link->getId()}";
            $text[] = "<b><i>Источник</i></b>: {$link->getSource()}";
            $text[] = "<b><i>Ссылка</i></b>: {$link->getUrl()}";
            $text = implode(PHP_EOL, $text);

            $likeButton = [];
            $likeButton['text'] = 'Удалить';
            $likeButton['callback_data'] = json_encode(['type' => 'link', 'action' => 'delete', 'linkId' => $link->getId()]);

//            $dislikeButton = [];
//            $dislikeButton['text'] = '👍️';
//            $dislikeButton['callback_data'] = json_encode(['type' => 'search', 'action' => 'like', 'userId' => $userBySearch->getId()]);

            $keyboards = new InlineKeyboard(
                [
                    $likeButton,
//                    $dislikeButton
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

}