<?php

namespace App\Module\UrlChecked\Entity;

use App\Module\Parser\Entity\ParseUrl;
use App\Module\Shared\Trait\Timestamps;
use App\Module\Telegram\Entity\TelegramUser;
use App\Module\UrlChecked\Repository\UrlCheckedRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UrlCheckedRepository::class)]
#[ORM\HasLifecycleCallbacks]
class UrlChecked
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180)]
    private string $url;

    #[ORM\ManyToOne(targetEntity: ParseUrl::class, inversedBy: "urlChecked")]
    private ParseUrl $parseUrl;

    #[ORM\ManyToOne(targetEntity: TelegramUser::class, inversedBy: 'urlChecked')]
    private TelegramUser $telegramUser;


    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return UrlChecked
     */
    public function setUrl(string $url): UrlChecked
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return ParseUrl
     */
    public function getParseUrl(): ParseUrl
    {
        return $this->parseUrl;
    }

    /**
     * @param ParseUrl $parseUrl
     * @return UrlChecked
     */
    public function setParseUrl(ParseUrl $parseUrl): UrlChecked
    {
        $this->parseUrl = $parseUrl;
        return $this;
    }

    /**
     * @param TelegramUser $user
     * @return UrlChecked
     */
    public function setUser(TelegramUser $user): UrlChecked
    {
        $this->telegramUser = $user;
        return $this;
    }

    /**
     * @return TelegramUser
     */
    public function getUser(): TelegramUser
    {
        return $this->telegramUser;
    }

}
