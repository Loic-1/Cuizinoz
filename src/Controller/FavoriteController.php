<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\FavoriteRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FavoriteController extends AbstractController
{
    #[Route('/favorite/{user}', name: 'app_favorite')]
    public function index(User $user, FavoriteRepository $favoriteRepository): Response
    {
        $favorites = $favoriteRepository->findFavorites($user->getId());

        return $this->render('favorite/index.html.twig', [
            'favorites' => $favorites
        ]);
    }
}
