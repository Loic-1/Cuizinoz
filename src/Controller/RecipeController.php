<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Comment;
use App\Entity\Note;
use App\Entity\User;
use App\Entity\Photo;
use App\Entity\Recipe;
use App\Form\CommentType;
use App\Form\NoteType;
use App\Form\RecipeType;
use App\Form\SearchType;
use App\Repository\RecipeRepository;
use App\Service\PictureService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

use function PHPUnit\Framework\isEmpty;

class RecipeController extends AbstractController
{
    // Renvoie le user spécifié pour récupérer ses recettes plus tard
    #[Route('/recipe/userRecipes/{id}', name: 'user_recipe')]
    public function userRecipes(User $user = null)
    {
        if ($user) {
            return $this->render('recipe/listUserRecipe.html.twig', [
                'user' => $user,
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    // Renvoie une liste des recipe, permet de créer une nouvelle recipe
    #[Route('/recipe/read', name: 'app_recipe')]
    public function listRecipes(RecipeRepository $recipeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $filterForm = $this->createForm(SearchType::class, $data);
        $filterForm->handleRequest($request);

        $recipes = $recipeRepository->findSearch($data);

        return $this->render('recipe/index.html.twig', [
            'filterForm' => $filterForm->createView(),
            'recipes' => $recipes,
            // 'pagination' => $pagination
        ]);
    }

    // Renvoie le form de création de recette
    #[Route('/recipe/edit/addRecipe', name: 'create_recipe')]
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

        return $this->render('recipe/createRecipe.html.twig', [
            'addRecipeForm' => $form
        ]);
    }

    // Permet de modifier la recipe spécifiée, puis de la renvoyer pour l'afficher
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

    #[Route('/recipe/random', name: 'random_recipe')]
    public function randomRecipe(RecipeRepository $recipeRepository)
    {
        $randomId = random_int(0, count($recipeRepository->findAll()) - 1);

        $recipe = $recipeRepository->find($randomId);

        if ($recipe) {
            return $this->redirectToRoute('detail_recipe', [
                'recipe' => $recipe->getId(),
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    // Renvoie la recipe spécifiée, permet de publier un comment pour la recipe
    #[Route('/recipe/{recipe}', name: 'detail_recipe')]
    public function detailRecipe(Recipe $recipe = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($recipe) {

            $avgNote = $recipe->getAverageNote();

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

            $note = new Note();

            $noteForm = $this->createForm(NoteType::class, $note);
            $noteForm->handleRequest($request);

            if ($noteForm->isSubmitted() && $noteForm->isValid()) {

                $note = $noteForm->getData();

                $note->setUser($this->getUser());
                $note->setRecipe($recipe);

                $entityManager->persist($note);
                $entityManager->flush();

                return $this->redirectToRoute('detail_recipe', [
                    'recipe' => $recipe->getId()
                ]);
            }

            return $this->render('recipe/detailRecipe.html.twig', [
                'recipe' => $recipe,
                'addCommentForm' => $commentForm,
                'addNoteForm' => $noteForm,
                'avgNote' => $avgNote,
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }
}
