<?php

declare(strict_types=1);

namespace App\Module\Admin\Controller\EasyAdmin;

use App\Module\Parser\Entity\ParseUrl;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;


class ParseUrlController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ParseUrl::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            BooleanField::new('isActive')->setCustomOption(BooleanField::OPTION_RENDER_AS_SWITCH, false),
            IntegerField::new('period'),
            TextField::new('source'),
            TextareaField::new('url'),
        ];
    }

}
