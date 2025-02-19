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
    #[Route('/category/{category}/{orderBy?}/{order?}', name: 'recipe_category', defaults:['orderBy' => 'note', 'ORDER' => 'DESC'])]
    public function index(Category $category = null, CategoryRepository $categoryRepository, $orderBy, $order): Response
    {
        if ($category) {

            // $recipes = $categoryRepository->findRecipesByCategoryId($category->getId());
            $recipes = $categoryRepository->findRecipesByCategoryId($category->getId(), $orderBy, $order);

            return $this->render('category/index.html.twig', [
                'recipes' => $recipes,
                'category' => $category
            ]);
        } else {

            return $this->redirectToRoute('app_home');
        }
    }
}
