<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Entity\User;
use App\Entity\Recipe;
use App\Repository\FavoriteRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
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
    #[Route('favorite/add/{recipe}/{user}', name: 'add_favorite')]
    public function addFavorite(Recipe $recipe, User $user, EntityManagerInterface $entityManager)
    {
        $favorite = new Favorite();

        $favorite->setUser($user);
        $favorite->setRecipe($recipe);
        $favorite->setRegisterDate(new DateTime());

        $entityManager->persist($favorite);
        $entityManager->flush();

        return $this->redirectToRoute("app_favorite", [
            "user" => $user->getId()
        ]);
    }
}
