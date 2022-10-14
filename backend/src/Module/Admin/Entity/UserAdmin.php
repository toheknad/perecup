<?php

namespace App\Module\Admin\Entity;

use App\Module\User\Entity\UserSuperClass;
use App\Module\Admin\Repository\UserAdminRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserAdminRepository::class)]
class UserAdmin extends UserSuperClass  implements PasswordAuthenticatedUserInterface, LegacyPasswordAuthenticatedUserInterface
{
    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(type: 'string')]
    private ?string $salt = null;

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }


    /**
     * @see LegacyPasswordAuthenticatedUserInterface
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @param string|null $salt
     * @return $this
     */
    public function setSalt(?string $salt): self
    {
        $this->salt = $salt;
        return $this;
    }

}
