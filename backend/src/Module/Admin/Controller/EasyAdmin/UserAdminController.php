<?php

declare(strict_types=1);

namespace App\Module\Admin\Controller\EasyAdmin;

use App\Module\Admin\Entity\UserAdmin;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserAdminController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserAdmin::class;
    }
}
