<?php

declare(strict_types=1);

namespace App\Module\Admin\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

//        if (!$user->isActive()) {
//            $ex = new DisabledException('User account is deactivated.');
//            $ex->setUser($user);
//            throw $ex;
//        }
    }

    public function checkPostAuth(UserInterface $user)
    {
    }
}
