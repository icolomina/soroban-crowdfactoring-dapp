<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class RootController extends AbstractController
{
    #[Route('/r', name: 'get_root_page', methods: ['GET'])]
    public function getRootPage(): Response
    {
        /**
         * @var User|UserInterface
         */
        $user = $this->getUser();
        if(!$user) {
            return new RedirectResponse($this->generateUrl('app_login'));
        }

        return ($user->isSaver())
            ? new RedirectResponse($this->generateUrl('get_user_contracts_page'))
            : new RedirectResponse($this->generateUrl('get_contracts_page'))
        ;
    }

    #[Route('', name: 'get_landing_page', methods: ['GET'])]
    public function getLandingPage(): Response
    {
        return $this->render('landing.html.twig');
    }
}