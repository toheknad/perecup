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

    #[ORM\Column(type: 'datetime')]
    private \DateTimeImmutable $from;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeImmutable $to;

    #[ORM\OneToOne(targetEntity: TelegramUser::class)]
    private TelegramUser $telegramUser;

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
}
