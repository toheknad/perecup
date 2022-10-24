<?php

namespace App\Module\Parser\Message;

use App\Module\Parser\Entity\ParseUrl;
use JetBrains\PhpStorm\Pure;

/**
 *  Сообщение очереди для парсинга ссылок на объявления
 */
class ParseUrlCheckedMessage
{
    public function __construct(
        public string $name,
        public int $price,
        public string $description,
        public string $time,
        public string $url,
        public string $baseUrl,
        public int $userId,
        public bool $isFirstCheck,
        public string $city,
        public string $image,
    )
    {
    }

    #[Pure]
    public static function test(): ParseUrlCheckedMessage
    {
        return new self(
            'test',
            123123,
            'descsdas',
            '23123123 time'
        );
    }
}
