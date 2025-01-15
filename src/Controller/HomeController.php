<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'app_home')]
    public function index(RecipeRepository $recipeRepository, CommentRepository $commentRepository): Response
    {
        // recettes à la une (trié en fonction de la note et des commentaires, définir un max(par défaut: 6))

        $recipes = $recipeRepository->findNewBestRecipes(6);

        // derniers commentaires (définir un max(par défaut: 6))

        $comments = $commentRepository->findLastComments(6);

        return $this->render('home/index.html.twig', [
            'recipes' => $recipes,
            'comments' => $comments
        ]);
    }
}
