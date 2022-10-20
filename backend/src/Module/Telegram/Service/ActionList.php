<?php
namespace App\Module\Telegram\Service;

use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class ActionList
{
   public const ADDING_LINK = 10; // добавление ссылки
   public const ADDING_LINK_NAME = 11; // добавление имени для ссылки

}