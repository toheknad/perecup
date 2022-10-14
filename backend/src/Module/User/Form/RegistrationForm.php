<?php declare(strict_types=1);

namespace App\Module\User\Form;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Форма при регистрации юзера
 */
class RegistrationForm
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(min: 1, max: 30)]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 30)]
    public string $username;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 30)]
    public string $password;

    public function __construct(string $email, string $username, string $password)
    {
        $this->email = $email;
        $this->password = $password;
        $this->username = $username;
    }
}