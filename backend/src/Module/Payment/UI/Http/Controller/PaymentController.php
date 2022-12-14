<?php

namespace App\Module\Payment\UI\Http\Controller;


use App\Module\Payment\Repository\PaymentRepository;
use App\Module\Subscribe\Entity\Subscribe;
use App\Module\Telegram\Entity\Payment;
use App\Module\Telegram\Repository\TelegramUserRepository;
use App\Module\Telegram\Service\MessageBuilder;
use App\Module\Telegram\Service\MessageHandleService;
use App\Module\Telegram\Service\TelegramClient;
use Doctrine\ORM\EntityManagerInterface;
use Longman\TelegramBot\Telegram;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use YooKassa\Client;


class PaymentController extends AbstractController
{
    private Telegram $telegram;

    public function __construct(
        protected TelegramUserRepository $telegramUserRepository,
        protected EntityManagerInterface $entityManager,
        TelegramClient $telegram,
    )
    {
        $this->telegram = $telegram->getClient();
    }

    #[Route('/payment/create', name: 'payment_create', methods: 'GET')]
    public function createPaymentLink(Request $request): Response
    {
        $userId = (int) $request->get('userId');
        $subscriptionType = (int) $request->get('subscriptionType');
        if ($userId && $subscriptionType) {
            $user = $this->telegramUserRepository->find($userId);
            $client = new Client();
            $paymentData = $this->definePriceAndDescription($subscriptionType);
            $client->setAuth( $_ENV['YOOKASSA_SHOP'],  $_ENV['YOOKASSA_PASSWORD']);
            $payment = $client->createPayment(
                array(
                    'amount' => array(
                        'value' => $paymentData['price'],
                        'currency' => 'RUB',
                    ),
                    'confirmation' => array(
                        'type' => 'redirect',
                        'return_url' => 'https://www.example.com/return_url',
                    ),
                    'capture' => true,
                    'description' => $paymentData['description'],
                ),
                uniqid('', true)
            );
            if ($payment) {
                $payment = $payment->toArray();
                $userPayment = new Payment();
                $userPayment->setId($payment['id']);
                $userPayment->setStatus($payment['status']);
                $userPayment->setSubscriptionType($subscriptionType);
                $userPayment->setTelegramUser($user);
                $this->entityManager->persist($userPayment);
                $this->entityManager->flush();
                return $this->redirect($payment['confirmation']['confirmation_url']);
            }
            throw new \Exception('??????-???? ?????????? ???? ??????');
        }
        return $this->json(['Somethins is wrong with userId or subscription type'], 400);
    }

    #[Route('/payment/yookassa/webhook', name: 'payment_yookassa_webhook', methods: 'POST')]
    public function webhookYookassa(
        Request $request,
        PaymentRepository $paymentRepository,
    ): Response
    {
//        file_put_contents('redirect.txt',  'TESDAD');
        $data = $request->toArray();

        if (!$data) {
            return $this->json(['Request is empty'], 200);
        }
        if (!isset($data['event'])) {
            return $this->json(['Array does not have event item'], 200);
        }

        if ($data['event'] === 'payment.succeeded') {
            $paymentUuid = $data['object']['id'];
            $paymentStatus = $data['object']['status'];
            $userPayment = $paymentRepository->find($paymentUuid);

            /** @var Payment $userPayment */
            if (!$userPayment) {
                return $this->json(['Users payment is not found'], 200);
            }

            $user = $userPayment->getTelegramUser();

            $userPayment->setStatus($paymentStatus);
            if ($userPayment->getSubscriptionType() == Subscribe::SUBSCRIBE_TYPE_ONE_WEEK) {
                $subscription = Subscribe::makeOneWeekSubscription($userPayment->getTelegramUser());
            } elseif ($userPayment->getSubscriptionType() == Subscribe::SUBSCRIBE_TYPE_TWO_WEEK) {
                $subscription = Subscribe::makeTwoWeekSubscription($userPayment->getTelegramUser());
            } elseif ($userPayment->getSubscriptionType() == Subscribe::SUBSCRIBE_TYPE_MONTH) {
                $subscription = Subscribe::makeOneMonthSubscription($userPayment->getTelegramUser());
            }

            $user->setSubscribe($subscription);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            MessageBuilder::subscriptionActivated($user, $userPayment->getSubscriptionType());
        }

        return $this->json(['Ok'], 200);
    }

    private function definePriceAndDescription(int $subscriptionType)
    {
        switch ($subscriptionType) {
            case Subscribe::SUBSCRIBE_TYPE_ONE_WEEK:
                return [
                    'price' => 390.0,
                    'description' => "???????????? ???????????????? ???? 1 ????????????",
                ];
            case Subscribe::SUBSCRIBE_TYPE_TWO_WEEK:
                return [
                    'price' => 590.0,
                    'description' => '???????????? ???????????????? ???? 2 ????????????',
                ];
            case Subscribe::SUBSCRIBE_TYPE_MONTH:
                return [
                    'price' => 890.0,
                    'description' => '???????????? ???????????????? ???? 1 ??????????',
                ];
        }
        throw new \Exception('???????????? ???????? ???????????????? ???? ????????????????????');
    }

}