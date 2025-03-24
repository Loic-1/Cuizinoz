<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Category;
use App\Form\SearchType;
use App\Repository\RecipeRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    // Renvoie une liste des recettes appartenant à la catégorie, ainsi que la catégorie
    #[Route('/category/detailCategory/{category}', name: 'detail_category')]
    public function detailCategory(Category $category = null, RecipeRepository $recipeRepository, Request $request): Response
    {
        if ($category) {

            $data = new SearchData();
            $data->page = $request->get('page', 1);

            $filterForm = $this->createForm(SearchType::class, $data);
            $filterForm->handleRequest($request);

            $data->addCategory($category);

            $recipes = $recipeRepository->findSearch($data);

            $metaDescription = "Vous aimez cuisiner ? Créez des recettes facilement, choisissez vos ingrédients et la description et publiez la recette pour en faire profiter tout le monde !";

            return $this->render('category/detailCategory.html.twig', [
                'category' => $category,
                'recipes' => $recipes,
                'filterForm' => $filterForm,
                'metaDescription' => $metaDescription,
            ]);
        } else {

            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/category/listCategory', name: 'list_category')]
    public function listCategory(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();

        $metaDescription = "Vous aimez cuisiner ? Retrouvez toutes les catégories des recettes présentes sur Cuizinoz, des apéritifs aux desserts, afin de satisfaire tous les gourmands !";

        return $this->render('category/listCategory.html.twig', [
            'categories' => $categories,
            'metaDescription' => $metaDescription,
        ]);
    }
}
