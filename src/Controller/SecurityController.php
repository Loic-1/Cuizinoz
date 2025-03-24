<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, RateLimiterFactory $loginLimiter, Request $request): Response
    {
        $limiter = $loginLimiter->create($request->getClientIp());

        if (false === $limiter->consume(1)->isAccepted()) {
            // $this->addFlash(
            //     'error',
            //     'Trop de tentatives de connexion, veuillez rééssayer dans '
            // );
            throw new TooManyRequestsHttpException();
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $metaDescription = "Vous aimez cuisiner ? Connectez-vous à votre compte pour accéder à plus de fonctionnalités : création de recettes, de collections et mise en favoris !";

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'metaDescription' => $metaDescription,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
