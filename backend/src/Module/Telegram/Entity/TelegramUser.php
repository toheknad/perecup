<?php

namespace App\Module\Telegram\Entity;

use App\Module\Parser\Entity\ParseUrl;
use App\Module\Subscribe\Entity\Subscribe;
use App\Module\User\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class TelegramUser
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer',unique: true)]
    private int $chatId;

    #[ORM\Column(type: 'integer')]
    private int $action = 0;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ParseUrl::class, cascade: ["remove"])]
    private Collection $parseUrls;

    #[ORM\OneToOne(targetEntity: Subscribe::class, cascade: ["persist", "remove"])]
    private ?Subscribe $subscribe = null;

    public function __construct()
    {
        $this->parseUrls = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $chatId
     * @return TelegramUser
     */
    public function setChatId(int $chatId): TelegramUser
    {
        $this->chatId = $chatId;
        return $this;
    }

    /**
     * @return int
     */
    public function getChatId(): int
    {
        return $this->chatId;
    }

    /**
     * @param ArrayCollection $parseUrls
     * @return TelegramUser
     */
    public function setParseUrls(ArrayCollection $parseUrls): TelegramUser
    {
        $this->parseUrls = $parseUrls;
        return $this;
    }

    public function setParseUrl(ParseUrl $parseUrls): TelegramUser
    {
        $this->parseUrls[] = $parseUrls;
        return $this;
    }

    /**
     * @return Collection|ParseUrl[]
     */
    public function getParseUrls(): Collection
    {
        return $this->parseUrls;
    }

    /**
     * @param int $action
     * @return TelegramUser
     */
    public function setAction(int $action): TelegramUser
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return int
     */
    public function getAction(): int
    {
        return $this->action;
    }

    /**
     * @return Subscribe
     */
    public function getSubscribe(): ?Subscribe
    {
        return $this->subscribe;
    }

    /**
     * @param Subscribe $subscribe
     * @return TelegramUser
     */
    public function setSubscribe(Subscribe $subscribe): TelegramUser
    {
        $this->subscribe = $subscribe;
        return $this;
    }

    #[Pure] public function isUserHasSubscribe(): bool
    {
        if ($this->getSubscribe() && !$this->isSubscriptionEnded()) {
            return true;
        }
        return false;
    }

    public function getMaxAmountLinks(): int
    {
        if ($this->getSubscribe()?->getType() === Subscribe::SUBSCRIBE_TYPE_TRIAL) {
            return 1;
        }

        if ($this->getSubscribe()?->getType() === Subscribe::SUBSCRIBE_TYPE_STANDART) {
            return 5;
        }
    }

    public function getAmountLinks(): int
    {
        return count($this->getParseUrls());
    }

    public function hasUserTrial(): bool
    {
        return $this->getSubscribe()->getType() === Subscribe::SUBSCRIBE_TYPE_TRIAL;
    }

    public function hasUserStandart(): bool
    {
        return $this->getSubscribe()->getType() === Subscribe::SUBSCRIBE_TYPE_STANDART;
    }

    public function isSubscriptionEnded(): bool
    {
        return (new \DateTimeImmutable()) > $this->getSubscribe()->getActivatedTo();
    }
}
