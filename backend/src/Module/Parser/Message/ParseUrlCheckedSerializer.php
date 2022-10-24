<?php

namespace App\Module\Parser\Message;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class ParseUrlCheckedSerializer implements SerializerInterface
{

    public function decode(array $encodedEnvelope): Envelope
    {
        $body = $encodedEnvelope['body'] ;
        $headers = $encodedEnvelope['headers'];

        $data = json_decode($body, true);

        if (null === $data) {
            throw new MessageDecodingFailedException('Invalid JSON');
        }

        // in case of redelivery, unserialize any stamps
        $stamps = [];
        $envelope = new Envelope(new ParseUrlCheckedMessage(
            $data['name'],
            (int)$data['price'],
            $data['description'],
            $data['time'],
            $data['url'],
            $data['baseUrl'],
            $headers['idUser'],
            (bool)$headers['isFirstCheck'],
            $data['city'],
            $data['image'],
        ));
        $envelope = $envelope->with(... $stamps);

        return $envelope;
    }

    public function encode(Envelope $envelope): array
    {
        // this is called if a message is redelivered for "retry"
        $message = $envelope->getMessage();
        // expand this logic later if you handle more than
        // just one message class
        $allStamps = [];
        foreach ($envelope->all() as $stamps) {
            $allStamps = array_merge($allStamps, $stamps);
        }
        return [
            'body' => json_encode($message, JSON_THROW_ON_ERROR),
            'headers' => [
                // store stamps as a header - to be read in decode()
                'stamps' => serialize($allStamps)
            ],
        ];
    }
}