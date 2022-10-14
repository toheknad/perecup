<?php

namespace App\Service\JWT;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

/**
 * Класс для генерации JWT токена, чтобы была единая точка для всех случаев
 */
class JWTGenerator
{
    public function __construct(
        private JWTTokenManagerInterface    $JWTManager,
    )
    {
    }

    public function generate(User $user): string
    {
        return $this->JWTManager->create($user);
    }
}