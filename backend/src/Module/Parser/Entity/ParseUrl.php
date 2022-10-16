<?php

namespace App\Module\Parser\Entity;

use App\Module\Parser\Repository\ParseUrlRepository;
use App\Module\Telegram\Entity\TelegramUser;
use App\Module\UrlChecked\Entity\UrlChecked;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParseUrlRepository::class)]
class ParseUrl
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 25)]
    private string $source;

    #[ORM\Column(type: 'text')]
    private string $url;

    #[ORM\Column(type: 'integer')]
    private int $period;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\ManyToOne(targetEntity: TelegramUser::class, inversedBy: 'parseUrls')]
    #[ORM\JoinColumn(name: 'telegram_user_id', referencedColumnName: 'id')]
    private TelegramUser $user;

    #[ORM\OneToMany(mappedBy: "parseUrl", targetEntity: UrlChecked::class, cascade: ["persist", "remove"])]
    private Collection $urlChecked;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getPeriod(): ?int
    {
        return $this->period;
    }

    public function setPeriod(int $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @param TelegramUser $user
     * @return ParseUrl
     */
    public function setUser(TelegramUser $user): ParseUrl
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return TelegramUser
     */
    public function getUser(): TelegramUser
    {
        return $this->user;
    }

}
