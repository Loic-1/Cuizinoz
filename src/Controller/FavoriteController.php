<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Recipe;
use App\Data\SearchData;
use App\Entity\Favorite;
use App\Form\SearchType;
use App\Repository\RecipeRepository;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FavoriteController extends AbstractController
{
    // Renvoir tous les favoris du user actuel
    #[Route('/favorite', name: 'app_favorite')]
    public function listOwnFavorites(FavoriteRepository $favoriteRepository, RecipeRepository $recipeRepository, Request $request): Response
    {
        $user = $this->getUser();

        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $filterForm = $this->createForm(SearchType::class, $data);
        $filterForm->handleRequest($request);

        $recipes = $recipeRepository->findSearch($data);
        // $recipes = $favoriteRepository->findSearch($data);

        return $this->render('favorite/index.html.twig', [
            'user' => $user,
            'filterForm' => $filterForm,
            'recipes' => $recipes,
        ]);
    }

    // Renvoie tous les favorites du user spécifié
    #[Route('/favorite/{user}', name: 'user_favorite')]
    public function listFavorites(User $user = null, FavoriteRepository $favoriteRepository, PaginatorInterface $paginator, Request $request): Response
    {
        if ($user) {

            $query = $favoriteRepository
                ->createQueryBuilder('f')
                ->where('f.user = userId')
                ->setParameter('userId', $user)
                ->getQuery();

            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                12,
            );

            return $this->render('favorite/index.html.twig', [
                'pagination' => $pagination,
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    // Permet de rajouter un favorite liant user à recipe
    // #[Route('favorite/edit/addFavorite/{user}/{recipe}', name: 'add_favorite')]
    // public function addFavorite(Recipe $recipe = null, User $user = null, FavoriteRepository $favoriteRepository, EntityManagerInterface $entityManager)
    // {
    //     if ($recipe && $user) {

    //         if (count($favoriteRepository->isUnique($recipe->getId())) == 0) {

    //             $favorite = new Favorite();

    //             $favorite->setUser($user);
    //             $favorite->setRecipe($recipe);
    //             $favorite->setRegisterDate(new DateTime());

    //             $entityManager->persist($favorite);
    //             $entityManager->flush();

    //             return $this->redirectToRoute("app_recipe");
    //         }

    //         return $this->redirectToRoute("app_favorite", [
    //             "user" => $user->getId()
    //         ]);
    //     } else {
    //         return $this->redirectToRoute('app_home');
    //     }
    // }

    #[Route('favorite/edit/addFavorite/{recipe}', name: 'add_favorite', methods: ["POST"])]
    public function addFavorite(Recipe $recipe, FavoriteRepository $favoriteRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(["error" => "user not found"], 404);
        }

        if (!$recipe) {
            return new JsonResponse(["error" => "recipe not found"], 404);
        }

        if (count($favoriteRepository->isUnique($recipe->getId(), $user)) > 0) {
            return new JsonResponse(["error" => "recipe already in favorite"], 400);
        }

        $favorite = new Favorite();

        $favorite->setRecipe($recipe);
        $favorite->setUser($user);
        $favorite->setRegisterDate(new \DateTime());

        $entityManager->persist($favorite);
        $entityManager->flush();

        return new JsonResponse(["success" => "favorite creation was successful"], 200);
    }

    // Permet de supprimer un favorite en fonction du user et de la recipe
    // #[Route('favorite/edit/removeFavorite/{user}/{recipe}/{favorite}', name: 'remove_favorite')]
    // public function removeFavorite(Recipe $recipe = null, User $user = null, Favorite $favorite = null, FavoriteRepository $favoriteRepository, EntityManagerInterface $entityManager)
    // {
    //     if ($user && $recipe && $favorite) {

    //         $user->removeFavorite($favorite);
    //         $recipe->removeFavorite($favorite);

    //         $entityManager->persist($user);
    //         $entityManager->persist($recipe);
    //         $entityManager->flush();

    //         return $this->redirectToRoute("app_favorite", [
    //             "user" => $user->getId()
    //         ]);
    //     } else {
    //         return $this->redirectToRoute('app_home');
    //     }
    // }

    #[Route('favorite/edit/removeFavorite/{recipe}/{favorite}', name: 'remove_favorite')]
    public function removeFavorite(Recipe $recipe = null, Favorite $favorite = null, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(["error" => "user not found"], 404);
        }

        if ($user != $favorite->getUser()) {
            return new JsonResponse(['error' => 'user not owner of favorite'], 403);
        }

        if (!$recipe || !$favorite) {
            return new JsonResponse(['error' => 'recipe or favorite not found'], 404);
        }

        $recipe->removeFavorite($favorite);

        $entityManager->persist($recipe);
        $entityManager->flush();

        return new JsonResponse(['success' => 'favorite removal was succesful'], 200);
    }
}
