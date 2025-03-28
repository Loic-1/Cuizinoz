<?php

namespace App\Controller;

use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Note;
use App\Entity\User;
use App\Entity\Photo;
use App\Entity\Recipe;
use App\Form\NoteType;
use App\Entity\Comment;
use App\Data\SearchData;
use App\Form\RecipeType;
use App\Form\SearchType;
use App\Form\CommentType;
use App\Repository\NoteRepository;
use App\Service\PictureService;
use App\Repository\RecipeRepository;
use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class RecipeController extends AbstractController
{
    // Renvoie le user spécifié pour récupérer ses recettes plus tard
    #[Route('/recipe/userRecipes/{id}', name: 'user_recipe')]
    public function userRecipes(User $user = null, RecipeRepository $recipeRepository, Request $request)
    {
        if ($user) {
            $data = new SearchData();
            $data->page = $request->get('page', 1);
            $filterForm = $this->createForm(SearchType::class, $data);
            $filterForm->handleRequest($request);

            $recipes = $recipeRepository->findSearch($data, $user->getId());

            $metaDescription = "Vous aimez cuisiner ? Découvrez toutes les recettes de " . $user->getPseudo() . " sur Cuizinoz, filtrez-les par catégorie, note ou nom, et explorez-les pour trouver l'inspiration !";

            return $this->render('recipe/listUserRecipe.html.twig', [
                'user' => $user,
                'filterForm' => $filterForm,
                'recipes' => $recipes,
                'metaDescription' => $metaDescription,
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    // Renvoie une liste des recipe, permet de créer une nouvelle recipe
    #[Route('/recipe/read', name: 'app_recipe')]
    public function listRecipes(RecipeRepository $recipeRepository, Request $request): Response
    {
        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $filterForm = $this->createForm(SearchType::class, $data);
        $filterForm->handleRequest($request);

        $recipes = $recipeRepository->findSearch($data);

        $metaDescription = "Vous aimez cuisiner ? Découvrez toutes les recettes de Cuizinoz, filtrez-les par catégorie, note ou nom, et explorez leurs saveurs pour trouver l'inspiration !";

        return $this->render('recipe/index.html.twig', [
            'filterForm' => $filterForm->createView(),
            'recipes' => $recipes,
            'metaDescription' => $metaDescription,
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

        $metaDescription = "Vous aimez cuisiner ? Créez votre recette en choisissant vos ingrédients, en rédigeant les instructions et en la publiant sur Cuizinoz pour la partager avec tous !";

        return $this->render('recipe/createRecipe.html.twig', [
            'addRecipeForm' => $form,
            'metaDescription' => $metaDescription,
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

            $metaDescription = "Vous aimez cuisiner ? Modifiez votre recette : photos, description, temps de préparation, ingrédients, et bien plus sur Cuizinoz !";

            return $this->render('recipe/editRecipe.html.twig', [
                'recipe' => $recipe,
                'editRecipeForm' => $form,
                'metaDescription' => $metaDescription,
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
    public function detailRecipe(Recipe $recipe = null, Request $request, EntityManagerInterface $entityManager, NoteRepository $nr, PaginatorInterface $paginator): Response
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

            $commentsData = [];
            foreach ($recipe->getComments() as $comment) {
                $commentsData[] = [
                    "comment" => $comment,
                    "note" => $nr->findUserNoteOnRecipeOrNull($comment->getUser(), $recipe)
                ];
            }

            $comments = $paginator->paginate(
                $commentsData,
                $request->query->getInt('page', 1),
                5,
            );

            $metaDescription = "Vous aimez cuisiner ? Découvrez la recette « " . $recipe->getName() . " » en détail, ses photos, ses instructions, ses commentaires et sa note, commentez et notez !";

            return $this->render('recipe/detailRecipe.html.twig', [
                'recipe' => $recipe,
                'addCommentForm' => $commentForm,
                'avgNote' => $avgNote,
                'comments' => $comments,
                'metaDescription' => $metaDescription,
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/downloadRecipe/{recipe}', name: 'download_recipe')]
    public function downloadRecipe(Recipe $recipe = null)
    {
        if ($recipe) {
            // options pdf
            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');

            // instance DomPdf avec options custom
            $domPdf = new Dompdf($pdfOptions);

            // définit template et paramètres
            $html = $this->renderView('special/recipePdf.html.twig', [
                'recipe' => $recipe,
            ]);

            // charge template
            $domPdf->loadHtml($html);

            $domPdf->render();

            // Récupération du contenu du PDF
            $output = $domPdf->output();

            // Renvoie une réponse Symfony avec le PDF
            return new Response($output, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $recipe->getName() . ' Cuizinoz.pdf"',
            ]);
        }
    }

    #[Route('/recipe/{recipe}/comments', name: 'list_comments_recipe')]
    public function listCommentsRecipe(NoteRepository $nr, Recipe $recipe = null, PaginatorInterface $paginator, Request $request)
    {
        if ($recipe) {

            $commentsData = [];
            foreach ($recipe->getComments() as $comment) {
                $commentsData[] = [
                    "comment" => $comment,
                    "note" => $nr->findUserNoteOnRecipeOrNull($comment->getUser(), $recipe)
                ];
            }

            $comments = $paginator->paginate(
                $commentsData,
                $request->query->getInt('page', 1),
                5,
            );

            $metaDescription = "Vous aimez cuisiner ? Découvrez les avis des utilisateurs sur la recette « " . $recipe->getName() . " » et partagez le vôtre en la notant et en commentant !";

            return $this->render('recipe/listCommentsRecipe.html.twig', [
                'recipe' => $recipe,
                'comments' => $comments,
                'metaDescription' => $metaDescription,
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/recipe/{recipe}/note/{notation}', name: 'note_recipe')]
    public function noteRecipe(Recipe $recipe = null, int $notation, NoteRepository $noteRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        if (!$recipe) {
            return new JsonResponse(['error' => 'Recipe not found'], 404);
        }

        // vérifier que le DOM n'a pas été modifié
        if ($notation > 5 || $notation < 0) {
            return new JsonResponse(['error' => 'Note not valid'], 400);
        }

        // première note de l'utilisateur sur cette recette => on ajoute
        if (!$noteRepository->findUserNoteOnRecipeOrNull($user->getId(), $recipe->getId())) {

            $note = new Note();
            $note->setUser($user);
            $note->setRecipe($recipe);
            $note->setNote($notation);

            $entityManager->persist($note);
            $entityManager->flush();

            return new JsonResponse([
                'success' => 'Note creation was succesful',
                'modified' => false,
                'avg' => $recipe->getAverageNote(),
            ], 200);
        }
        // pas la première note de l'utilisateur sur cette recette => on modifie
        else {
            // récupération du user déjà existant
            $note = $noteRepository->findUserNoteOnRecipeOrNull($user->getId(), $recipe->getId());

            $note->setNote($notation);

            $entityManager->persist($note);
            $entityManager->flush();

            return new JsonResponse([
                'success' => 'Note modification was succesful',
                'modified' => true,
                'avg' => $recipe->getAverageNote(),
            ], 200);
        }
    }
}
