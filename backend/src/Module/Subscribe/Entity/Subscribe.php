<?php

namespace App\Module\Subscribe\Entity;

use App\Module\Parser\Entity\ParseUrl;
use App\Module\Shared\Trait\Timestamps;
use App\Module\Telegram\Entity\TelegramUser;
use App\Module\User\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class Subscribe
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $activatedFrom;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $activatedTo;

    #[ORM\ManyToOne(targetEntity: TelegramUser::class, inversedBy: 'subscribe')]
    private TelegramUser $telegramUser;

    #[ORM\Column(type: 'integer')]
    private int $type;

    public const SUBSCRIBE_TYPE_TRIAL = 1;
    public const SUBSCRIBE_TYPE_ONE_WEEK = 2;
    public const SUBSCRIBE_TYPE_TWO_WEEK = 3;
    public const SUBSCRIBE_TYPE_MONTH = 4;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param \DateTimeImmutable $from
     * @return Subscribe
     */
    public function setFrom(\DateTimeImmutable $from): Subscribe
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getFrom(): \DateTimeImmutable
    {
        return $this->from;
    }

    /**
     * @param \DateTimeImmutable $to
     * @return Subscribe
     */
    public function setTo(\DateTimeImmutable $to): Subscribe
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getTo(): \DateTimeImmutable
    {
        return $this->to;
    }

    /**
     * @return TelegramUser
     */
    public function getTelegramUser(): TelegramUser
    {
        return $this->telegramUser;
    }

    /**
     * @param TelegramUser $telegramUser
     */
    public function setTelegramUser(TelegramUser $telegramUser): void
    {
        $this->telegramUser = $telegramUser;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return Subscribe
     */
    public function setType(int $type): Subscribe
    {
        $this->type = $type;
        return $this;
    }

    public function setTrial(): self
    {
        $this->setType(self::SUBSCRIBE_TYPE_TRIAL);
        $this->setActivatedFrom(new \DateTimeImmutable());
        $this->setActivatedTo((new \DateTimeImmutable())->modify('+2 days'));

        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeImmutable $createdAt
     * @return Subscribe
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): Subscribe
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getActivatedFrom(): \DateTimeImmutable
    {
        return $this->activatedFrom;
    }

    /**
     * @param \DateTimeImmutable $activatedFrom
     * @return Subscribe
     */
    public function setActivatedFrom(\DateTimeImmutable $activatedFrom): Subscribe
    {
        $this->activatedFrom = $activatedFrom;
        return $this;
    }

    /**
     * @param \DateTimeImmutable $activatedTo
     * @return Subscribe
     */
    public function setActivatedTo(\DateTimeImmutable $activatedTo): Subscribe
    {
        $this->activatedTo = $activatedTo;
        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getActivatedTo(): \DateTimeImmutable
    {
        return $this->activatedTo;
    }

    public static function makeOneWeekSubscription(TelegramUser $telegramUser): Subscribe
    {
        $subscription = new self();
        $subscription->setTelegramUser($telegramUser);
        $subscription->setType(self::SUBSCRIBE_TYPE_ONE_WEEK);
        $subscription->setActivatedFrom(new \DateTimeImmutable());
        $subscription->setActivatedTo((new \DateTimeImmutable())->modify('+7 days'));

        return $subscription;
    }

    public static function makeTwoWeekSubscription(TelegramUser $telegramUser): Subscribe
    {
        $subscription = new self();
        $subscription->setTelegramUser($telegramUser);
        $subscription->setType(self::SUBSCRIBE_TYPE_TWO_WEEK);
        $subscription->setActivatedFrom(new \DateTimeImmutable());
        $subscription->setActivatedTo((new \DateTimeImmutable())->modify('+14 days'));

        return $subscription;
    }

    public static function makeOneMonthSubscription(TelegramUser $telegramUser): Subscribe
    {
        $subscription = new self();
        $subscription->setTelegramUser($telegramUser);
        $subscription->setType(self::SUBSCRIBE_TYPE_MONTH);
        $subscription->setActivatedFrom(new \DateTimeImmutable());
        $subscription->setActivatedTo((new \DateTimeImmutable())->modify('+1 month'));

        return $subscription;
    }

}
