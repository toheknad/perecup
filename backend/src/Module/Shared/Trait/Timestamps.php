<?php
namespace App\Module\Shared\Trait;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

trait Timestamps
{
    #[ORM\Column(type: 'datetime', nullable:true)]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable:true)]
    private $updatedAt;

    final public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    final public function setCreatedAt(?DateTimeInterface $timestamp): self
    {
        $this->createdAt = $timestamp;

        return $this;
    }

    final public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    final public function setUpdatedAt(?DateTimeInterface $timestamp): self
    {
        $this->updatedAt = $timestamp;

        return $this;
    }

    #[ORM\PrePersist]
    final public function setCreatedAtAutomatically()
    {
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable());
        }
        $this->setUpdatedAt(new \DateTimeImmutable());
    }

    #[ORM\PreUpdate]
    final public function setUpdatedAtAutomatically()
    {
        $this->setUpdatedAt(new \DateTimeImmutable());
    }
}
