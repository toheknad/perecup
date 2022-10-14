<?php declare(strict_types=1);

namespace App\Module\User\Form;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Форма при логине юзера
 */
class LoginForm
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 30)]
    public string $username;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 30)]
    public string $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }
}