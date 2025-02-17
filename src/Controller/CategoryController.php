<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    // Renvoie une liste des recettes appartenant à la catégorie, ainsi que la catégorie
    #[Route('/category/{category}', name: 'recipe_category')]
    public function index(Category $category = null, CategoryRepository $categoryRepository): Response
    {
        if ($category) {

            $recipes = $categoryRepository->findRecipesByCategoryId($category->getId());

            return $this->render('category/index.html.twig', [
                'recipes' => $recipes,
                'category' => $category
            ]);
        } else {

            return $this->redirectToRoute('app_home');
        }
    }
}
