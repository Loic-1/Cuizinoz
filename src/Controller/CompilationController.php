<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CompilationController extends AbstractController
{
    #[Route('/compilation/{user}', name: 'app_compilation')]
    public function index(User $user): Response
    {
        $compilations = $user->getCompilations();

        return $this->render('compilation/index.html.twig', [
            'compilations' => $compilations
        ]);
    }
}
