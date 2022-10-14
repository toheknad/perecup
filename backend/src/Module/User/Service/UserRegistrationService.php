<?php

namespace App\Module\User\Service;

use App\Module\User\Entity\User;
use App\Module\User\Form\RegistrationForm;
use App\Service\JWT\JWTGenerator;
use App\Service\Validator\ValidatorObject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRegistrationService
{
    public function __construct(
        private JWTGenerator $generator,
        private UserPasswordHasherInterface $hasher,
        private ValidatorObject $validator,
        private EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function register(string $username, string $email, string $password): array
    {
        $registrationDto = new RegistrationForm(
            $email,
            $username,
            $password
        );

        $this->validator->validateOrFail($registrationDto);

        $user = new User();
        $user->setPassword($this->hasher->hashPassword($user, $registrationDto->password));
        $user->setEmail($registrationDto->email);
        $user->setUsername($registrationDto->email);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return [
            'token' => $this->generator->generate($user)
        ];
    }
}