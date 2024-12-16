<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Form\CommentType;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class RecipeController extends AbstractController
{
    #[Route('/recipe', name: 'app_recipe')]
    public function index(RecipeRepository $recipeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // trouver toutes les recettes
        $recipes = $recipeRepository->findAll();

        $user = $this->getUser();
        $recipe = new Recipe();

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $recipe = $form->getData();
            $recipe->setUser($user);

            $entityManager->persist($recipe);
            $entityManager->flush();

            // recharge la liste des recettes pour que la nouvelle recette s'affiche directement
            return $this->redirectToRoute("app_recipe");
        }

        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
            'addRecipeForm' => $form
        ]);
    }

    #[Route('/recipe/{recipe}', name: 'detail_recipe')]
    public function detailRecipe(Recipe $recipe, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment = $form->getData();
            $comment->setCreationDate(new DateTime());
            $comment->setUser($this->getUser());
            $comment->setRecipe($recipe);

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute("detail_recipe", [
                "recipe" => $recipe->getId()
            ]);
        }

        return $this->render('recipe/detailRecipe.html.twig', [
            'recipe' => $recipe,
            'addCommentForm' => $form
        ]);
    }

    #[Route('/recipe/edit/{recipe}', name: 'edit_recipe')]
    public function editRecipe(Recipe $recipe, Request $request, EntityManagerInterface $entityManager): Response
    {
        // le 2ème paramètre permet de "préremplir" le form avec les données de $recipe
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // on récupère les entrées dans les champs du form
            $recipe = $form->getData();

            // on prépare le push vers la BDD
            $entityManager->persist($recipe);
            // on push les données d'un coup, rapide et efficace -> https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/reference/working-with-objects.html#persisting-entities
            $entityManager->flush();

            return $this->redirectToRoute("detail_recipe", [
                "recipe" => $recipe->getId()
            ]);
        }

        return $this->render('recipe/editRecipe.html.twig', [
            'recipe' => $recipe,
            'editRecipeForm' => $form
        ]);
    }
}
