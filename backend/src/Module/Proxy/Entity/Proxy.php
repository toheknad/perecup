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

}
