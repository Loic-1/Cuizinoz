<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\NoteRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    // Renvoie un assortiment de recipe, comment et une liste de toutes les category
    #[Route('/', name: 'app_home')]
    public function listUsers(RecipeRepository $recipeRepository, CommentRepository $commentRepository, NoteRepository $nr, CategoryRepository $categoryRepository): Response
    {
        // Recettes à la une (trié en fonction de la note [et du nombre de commentaires], définir un max(par défaut: 6))

        $recipes = $recipeRepository->findBy([], [], 6);

        // Derniers commentaires (définir un max(par défaut: 6))

        $comments = $commentRepository->findBy([], ["creationDate" => "DESC"], 6);

        foreach($comments as $comment)
        {   
            $commentsData[] = [
                "comment" => $comment,
                "note" => $nr->findUserNoteOnRecipeOrNull($comment->getUser(), $comment->getRecipe())
            ];
        }

        // Catégories (pas de limite car peu de catégories)

        $categories = $categoryRepository->findAll();

        $metaDescription = "Vous aimez cuisiner ? Cuizinoz est là pour vous aider à trouver la recette idéale à toutes les occasions - N'hésitez plus et faites un essai dès maintenant !";

        return $this->render('home/index.html.twig', [
            'recipes' => $recipes,
            'comments' => $commentsData,
            'categories' => $categories,
            'metaDescription' => $metaDescription,
        ]);
    }
}
