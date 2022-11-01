<?php

namespace App\Module\Telegram\UI\Http\Controller;


use App\Module\Telegram\Service\MessageHandleService;
use App\Module\Telegram\Service\TelegramClient;
use Longman\TelegramBot\Telegram;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TelegramController extends AbstractController
{
    private Telegram $telegram;
    private MessageHandleService $messageHandleService;

    public function __construct(TelegramClient $telegram, MessageHandleService $messageHandleService)
    {
        $this->telegram = $telegram->getClient();
        $this->messageHandleService = $messageHandleService;
    }

    #[Route('/telegram/get-messages', name: 'telegram_get_messages', methods: 'GET')]
    public function index(Request $request): JsonResponse
    {
//        $messages = $this->telegram->handleGetUpdates()->getRawData();
//        foreach ($messages['result'] as $message) {
//            $this->messageHandleService->start($message);
//        }

        $data = sprintf(
            "%s %s %s\n\nHTTP headers:\n",
            $_SERVER['REQUEST_METHOD'],
            $_SERVER['REQUEST_URI'],
            $_SERVER['SERVER_PROTOCOL']
        );

        $headerList = [];
        foreach ($_SERVER as $name => $value) {
            if (preg_match('/^HTTP_/', $name)) {
                // convert HTTP_HEADER_NAME to Header-Name
                $name = strtr(substr($name, 5), '_', ' ');
                $name = ucwords(strtolower($name));
                $name = strtr($name, ' ', '-');

                // add to list
                $headerList[$name] = $value;
            }
        }

        foreach ($headerList as $name => $value) {
            $data .= $name . ': ' . $value . "\n";
        }

        $data .= "\nRequest body:\n";

        return $this->json($data, 200);
    }

}