<?php

namespace App\Module\Parser\Message;

use App\Module\Parser\Entity\ParseUrl;
use JetBrains\PhpStorm\Pure;

/**
 *  Сообщение очереди для парсинга ссылок на объявления
 */
class ParseUrlMessage
{

    public function __construct(
        public string  $source,              // Источник/сайт для парсинга
        public string  $url,                 // URL адрес страницы для парсинга
        public int  $userId,                 // URL адрес страницы для парсинга
        public string  $proxy,                  // URL адрес страницы для парсинга
        public bool  $isFirstCheck                  // URL адрес страницы для парсинга
    )
    {
    }

    #[Pure]
    public static function createFromEntity(ParseUrl $item, string $proxy, bool $isFirstCheck): ParseUrlMessage
    {
        return new self(
            $item->getSource(),
            $item->getUrl(),
            $item->getUser()->getId(),
            $proxy,
            $isFirstCheck
        );
    }
}
