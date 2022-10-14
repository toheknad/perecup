<?php
declare(strict_types=1);

namespace App\Service\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorObject
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validateOrFail(object $object): void
    {
        $errors = $this->validator->validate($object);

        if (count($errors)) {
            throw new \Exception((string)$errors);
        }
    }
}