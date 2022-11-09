<?php

namespace App\Module\Telegram\Entity;

use App\Module\Parser\Entity\ParseUrl;
use App\Module\Payment\Repository\PaymentRepository;
use App\Module\Shared\Trait\Timestamps;
use App\Module\Subscribe\Entity\Subscribe;
use App\Module\User\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Payment
{
    use Timestamps;

    //uuid
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $id;

    #[ORM\Column(type: 'string')]
    private string $status;

    #[ORM\ManyToOne(targetEntity: TelegramUser::class)]
    private TelegramUser $telegramUser;

    #[ORM\Column(type: 'integer')]
    private int $subscriptionType;

    public const STATUS_PENDING = 'pending';
    public const STATUS_SUCCEEDED = 'succeeded';
    public const STATUS_CANCELED = 'canceled';

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param int $subscriptionType
     * @return Payment
     */
    public function setSubscriptionType(int $subscriptionType): Payment
    {
        $this->subscriptionType = $subscriptionType;
        return $this;
    }

    /**
     * @param string $id
     * @return Payment
     */
    public function setId(string $id): Payment
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $status
     * @return Payment
     */
    public function setStatus(string $status): Payment
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param TelegramUser $telegramUser
     * @return Payment
     */
    public function setTelegramUser(TelegramUser $telegramUser): Payment
    {
        $this->telegramUser = $telegramUser;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return TelegramUser
     */
    public function getTelegramUser(): TelegramUser
    {
        return $this->telegramUser;
    }

    /**
     * @return int
     */
    public function getSubscriptionType(): int
    {
        return $this->subscriptionType;
    }
}
