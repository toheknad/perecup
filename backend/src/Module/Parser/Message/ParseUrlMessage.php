<?php

namespace App\Module\Parser\Message;

use App\Module\Parser\Entity\ParseUrl;
use JetBrains\PhpStorm\Pure;

/**
 *  Сообщение очереди для парсинга ссылок на объявления
 */
class ParseUrlMessage
{
    /** @var string|null Прокси сервер */
    public ?string $proxy;

    public function __construct(
        public string  $source,              // Источник/сайт для парсинга
        public string  $url,                 // URL адрес страницы для парсинга
        public int  $userId                  // URL адрес страницы для парсинга
    )
    {
    }

    #[Pure]
    public static function createFromEntity(ParseUrl $item): ParseUrlMessage
    {
        return new self(
            $item->getSource(),
            $item->getUrl(),
            $item->getUser()->getId()
        );
    }
}
