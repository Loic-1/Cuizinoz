<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\AddRecipeType;
use App\Repository\RecipeRepository;
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


        $form = $this->createForm(AddRecipeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $recipeData = $form->getData();

            $entityManager->persist($recipeData);
            $entityManager->flush();

            // permet de recharger la liste des recettes pour que la nouvelle recette s'affiche directement
            return $this->redirectToRoute("app_recipe");
        }


        // $name = $request->request->get('name');

        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
            'addRecipeForm' => $form
        ]);
    }
}
