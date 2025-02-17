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
    // Renvoie tous les favorites du user spécifié
    #[Route('/favorite/{user}', name: 'app_favorite')]
    public function listFavorites(User $user = null, FavoriteRepository $favoriteRepository): Response
    {
        if ($user) {

            $favorites = $favoriteRepository->findFavorites($user->getId());

            return $this->render('favorite/index.html.twig', [
                'favorites' => $favorites
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    // Permet de rajouter un favorite liant user à recipe
    #[Route('favorite/edit/addFavorite/{user}/{recipe}', name: 'add_favorite')]
    public function addFavorite(Recipe $recipe = null, User $user = null, FavoriteRepository $favoriteRepository, EntityManagerInterface $entityManager)
    {
        if ($recipe && $user) {

            if (count($favoriteRepository->isUnique($recipe->getId())) == 0) {

                $favorite = new Favorite();

                $favorite->setUser($user);
                $favorite->setRecipe($recipe);
                $favorite->setRegisterDate(new DateTime());

                $entityManager->persist($favorite);
                $entityManager->flush();

                return $this->redirectToRoute("app_recipe");
            }

            return $this->redirectToRoute("app_favorite", [
                "user" => $user->getId()
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    // Permet de supprimer un favorite en fonction du user et de la recipe
    #[Route('favorite/edit/removeFavorite/{user}/{recipe}/{favorite}', name: 'remove_favorite')]
    public function removeFavorite(Recipe $recipe = null, User $user = null, Favorite $favorite = null, FavoriteRepository $favoriteRepository, EntityManagerInterface $entityManager)
    {
        if ($user && $recipe && $favorite) {

            $user->removeFavorite($favorite);
            $recipe->removeFavorite($favorite);

            $entityManager->persist($user);
            $entityManager->persist($recipe);
            $entityManager->flush();

            return $this->redirectToRoute("app_favorite", [
                "user" => $user->getId()
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }
}
