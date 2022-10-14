<?php

namespace App\Module\User\UI\Http\Controller;

use App\Module\User\Service\UserLoginService;
use App\Module\User\Service\UserRegistrationService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route('/api/login_check', name: 'api_login_check', methods: 'POST')]
    public function index(
        Request                     $request,
        UserLoginService            $loginService
    ): JsonResponse
    {
        try {
            return $this->json(
                $loginService->login(
                    $request->get('username'),
                    $request->get('password')
                )
            );
        } catch (\Exception $exception) {
            return $this->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @throws \JsonException
     */
    #[Route('/api/register', name: 'register', methods: 'POST')]
    public function register(Request $request, UserRegistrationService $registrationService): JsonResponse
    {
       $request = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        try {
            return $this->json(
                $registrationService->register(
                    $request['username'],
                    $request['email'],
                    $request['password']
                )
            );
        } catch (\Exception $exception) {
            return $this->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    #[Route('/api/test2', name: 'api.test.2')]
    public function test(JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        return new JsonResponse(['test' => $this->getUser()->getUserIdentifier()]);
    }
}