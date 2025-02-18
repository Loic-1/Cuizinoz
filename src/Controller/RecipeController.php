<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Photo;
use App\Entity\Recipe;
use App\Form\CommentType;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use App\Service\PictureService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Image;
use Symfony\Component\HttpFoundation\Request;

class RecipeController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/recipe', name: 'app_recipe')]
    public function index(RecipeRepository $recipeRepository, Request $request, EntityManagerInterface $entityManager, PictureService $pictureService): Response
    {
        // trouver toutes les recettes
        $recipes = $recipeRepository->findAll();

        $user = $this->getUser();
        $recipe = new Recipe();

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On récupère les images
            $images = $form->get('images')->getData();
            foreach ($images as $image) {
                // On définit le dossier de destination
                $folder = 'gallery';

                // On appelle le service d'ajout
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $photo = new Photo();
                $photo->setName($fichier);
                $recipe->addPhoto($photo);
            }

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

    #[IsGranted('ROLE_USER')]
    #[Route('/createRecipe', name: 'create_recipe')]
    public function createRecipe(Request $request, EntityManagerInterface $entityManager, PictureService $pictureService): Response
    {
        $user = $this->getUser();
        $recipe = new Recipe();

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On récupère les images
            $images = $form->get('images')->getData();
            foreach ($images as $image) {
                // On définit le dossier de destination
                $folder = 'gallery';

                // On appelle le service d'ajout
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $photo = new Photo();
                // $photo->setName($fichier);
                $recipe->addPhoto($photo);
            }

            $recipe = $form->getData();
            $recipe->setUser($user);

            $entityManager->persist($recipe);
            $entityManager->flush();

            // recharge la liste des recettes pour que la nouvelle recette s'affiche directement
            return $this->redirectToRoute("app_recipe");
        }

        return $this->render('recipe/createRecipe.html.twig', [
            'addRecipeForm' => $form
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/recipe/{recipe}', name: 'detail_recipe')]
    public function detailRecipe(Recipe $recipe = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($recipe) {
            $comment = new Comment();

            $commentForm = $this->createForm(CommentType::class, $comment);
            $commentForm->handleRequest($request);

            if ($commentForm->isSubmitted() && $commentForm->isValid()) {

                $comment = $commentForm->getData();
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
                'addCommentForm' => $commentForm
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/recipe/edit/{recipe}', name: 'edit_recipe')]
    public function editRecipe(Recipe $recipe = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($recipe) {

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
        } else {
            return $this->redirectToRoute('app_home');
        }
    }
}
