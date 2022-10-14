<?php

namespace App\Module\User\Service;

use App\Module\User\Form\LoginForm;
use App\Module\User\Repository\UserRepository;
use App\Service\JWT\JWTGenerator;
use App\Service\Validator\ValidatorObject;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserLoginService
{
    public function __construct(
        private JWTGenerator $generator,
        private UserPasswordHasherInterface $hasher,
        private UserRepository $userRepository,
        private ValidatorObject $validator
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function login(string $username, string $password)
    {
        $loginDto = new LoginForm(
            $username,
            $password
        );

        $this->validator->validateOrFail($loginDto);

        if (!$user = $this->userRepository->findOneBy(['username' => $loginDto->username])) {
            throw new \InvalidArgumentException("User isn't found");
        }

        $isValidUser = $this->hasher->isPasswordValid($user, $password);

        if (!$isValidUser) {
            throw new \InvalidArgumentException("Credentials is wrong");
        }

        return [
            'user' => $user->getUserIdentifier(),
            'token' => $this->generator->generate($user)
        ];
    }
}