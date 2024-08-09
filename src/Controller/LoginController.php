<?php

namespace App\Controller;

use App\Api\User\RegisterUserService;
use App\Dto\Input\RegisterUserDtoInput;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/register-user-data', name: 'api_register_user', methods: ['POST'])]
    public function postRegisterUserData(#[MapRequestPayload] RegisterUserDtoInput $registerUserDtoInput, RegisterUserService $registerUserService): JsonResponse
    {
        $registerUserService->registerUser($registerUserDtoInput);
        return new JsonResponse(null, 204);
    }

    #[Route('/register', name: 'get_register_page', methods: ['GET'])]
    public function getRegisterPage(): Response
    {
        return $this->render('register.html.twig');
    }

    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);

    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
