<?php

declare(strict_types=1);

namespace App\Module\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/adm360')]
class SecurityController extends AbstractController
{
    #[Route('/login', name: 'adm360_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('adm360');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@EasyAdmin/page/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
            'translation_domain' => 'admin',
            'page_title' => 'ACME login',
            'csrf_token_intention' => 'authenticate',
            'target_path' => $this->generateUrl('adm360'),
            'username_label' => 'Your username',
            'password_label' => 'Your password',
            'sign_in_label' => 'Log in',
            'username_parameter' => 'username',
            'password_parameter' => 'password',
        ]);
    }

    #[Route('/logout', name: 'adm360_logout')]
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
