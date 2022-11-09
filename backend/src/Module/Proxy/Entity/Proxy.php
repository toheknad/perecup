<?php

namespace App\Module\Proxy\Entity;

use App\Module\Proxy\Repository\ProxyRepository;
use App\Module\Shared\Trait\Timestamps;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProxyRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Proxy
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180)]
    private string $proxy;

    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    private ?string $login;

    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    private ?string $password;

    #[ORM\Column(type: 'boolean')]
    private bool $status;

    /**
     * @return string
     */
    public function getProxy(): string
    {
        return $this->proxy;
    }

    /**
     * @param string $proxy
     * @return Proxy
     */
    public function setProxy(string $proxy): Proxy
    {
        $this->proxy = $proxy;
        return $this;
    }

    /**
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     * @return Proxy
     */
    public function setStatus(bool $status): Proxy
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLogin(): ?string
    {
        return $this->login;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $login
     * @return Proxy
     */
    public function setLogin(?string $login): Proxy
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @param string|null $password
     * @return Proxy
     */
    public function setPassword(?string $password): Proxy
    {
        $this->password = $password;
        return $this;
    }

    public function getArray()
    {
        return [
            'ip' => $this->getProxy(),
            'login' => $this->login,
            'password' => $this->password,
        ];
    }

}
