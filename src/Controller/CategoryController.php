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
    public function index(Category $category = null): Response
    {
        if ($category) {
            $recipes = $category->getRecipes();

            return $this->render('category/index.html.twig', [
                'recipes' => $recipes,
                'category' => $category
            ]);
        } else {

            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/category/listCategory', name: 'list_category')]
    public function listCategory(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/listCategory.html.twig', [
            'categories' => $categories,
        ]);
    }
}
