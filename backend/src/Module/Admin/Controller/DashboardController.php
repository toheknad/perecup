<?php

declare(strict_types=1);

namespace App\Module\Admin\Controller;

use App\Module\Admin\Controller\EasyAdmin\UserAdminController;
use App\Module\Admin\Controller\EasyAdmin\ParseUrlController;
use App\Module\Admin\Entity\UserAdmin;
use App\Module\Parser\Entity\ParseUrl;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{

    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    #[Route('/adm360', name: 'adm360')]
    public function index(): Response
    {
        return $this->redirect($this->adminUrlGenerator->setController(UserAdminController::class)->generateUrl());
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToCrud('Users', 'fa fa-user', UserAdmin::class),
            MenuItem::linkToCrud('ParseUrl', 'fa fa-star', ParseUrl::class),
        ];
    }
}
