<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Compilation;
use App\Entity\Recipe;
use App\Entity\Save;
use App\Form\CompilationType;
use App\Repository\CompilationRepository;
use App\Repository\RecipeRepository;
use App\Repository\SaveRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompilationController extends AbstractController
{
    // Renvoie toutes les compilations
    #[Route('/compilations', name: 'list_compilation')]
    public function listCompilations(CompilationRepository $compilationRepository): Response
    {

        // trouve toutes les compilations stockées en BDD
        $compilations = $compilationRepository->findAll();

        return $this->render('compilation/listCompilation.html.twig', [
            'compilations' => $compilations
        ]);
    }

    // Renvoie le user spécifié (pour afficher ses compilations) (POTENTIELLEMENT À REFAIRE)
    #[Route('/compilation/{user}', name: 'app_compilation')]
    public function listCompilationsUser(User $user = null): Response
    {

        if ($user) {

            return $this->render('compilation/index.html.twig', [
                'user' => $user
            ]);
        } else {

            return $this->redirectToRoute("app_home");
        }
    }

    // Permet de créer une compilation pour le user spécifié
    #[Route('/compilation/edit/addCompilation/{user}', name: 'create_compilation')]
    public function createCompilation(User $user = null, Request $request, EntityManagerInterface $entityManager): Response
    {

        if ($user) {

            $compilation = new Compilation();

            $form = $this->createForm(CompilationType::class, $compilation);
            // on demande à $form d'inspecter la requête envoyée et d'appeler Symfony\Component\Form\FormInterface::submit si le bouton submit est cliqué
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                // Récupèration des données entrées dans les champs
                $compilation = $form->getData();
                $compilation->setUser($user);
                $compilation->setCreationDate(new DateTime());

                // Préparation du push puis push sur BDD
                $entityManager->persist($compilation);
                $entityManager->flush();

                return $this->redirectToRoute('app_compilation', [
                    'user' => $user->getId()
                ]);
            }

            // Envoi du form tant qu'il n'est pas submit pour permettre son affichage
            return $this->render('compilation/addCompilation.html.twig', [
                'addCompilationForm' => $form
            ]);
        } else {
            return $this->redirectToRoute("app_home");
        }
    }

    // Permet de sauvegarder une compilation pour le user spécifié
    #[Route('/compilation/edit/saveCompilation/{user}/{compilation}', name: 'add_compilation')]
    public function addSave(Compilation $compilation = null, User $user = null, EntityManagerInterface $entityManager, SaveRepository $saveRepository): Response
    {
        if ($compilation && $user) {

            // Si une save associée à cette compilation n'existe pas déjà
            if (count($saveRepository->isUnique($compilation->getId())) == 0) {

                $save = new Save();

                // Définition des attributs
                $save->setUser($user);
                $save->setCompilation($compilation);
                $save->setRegisterDate(new DateTime());

                // Préparation du push puis push sur BDD
                $entityManager->persist($save);
                $entityManager->flush();

                return $this->redirectToRoute("list_compilation");
            }

            return $this->render("compilation/index.html.twig", [
                "user" => $user
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    // Permet de retirer une sauvegarde pour le user spécifié
    #[Route('/compilation/edit/removeSave/{user}/{save}', name: 'remove_compilation')]
    public function removeSave(Save $save = null, User $user = null, EntityManagerInterface $entityManager): Response
    {
        if ($user && $save) {

            $user->removeSave($save);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute("app_compilation", [
                "user" => $user->getId()
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    // Permet d'afficher le détail d'une compilation
    #[Route('/compilation/detail/{compilation}', name: 'detail_compilation')]
    public function detailCompilation(Compilation $compilation = null, RecipeRepository $recipeRepository, Request $request, PaginatorInterface $paginator): Response
    {
        if ($compilation) {

            $recipes = $compilation->getRecipes();
            // $query = $recipeRepository->createQueryBuilder('r')->getQuery();

            // $pagination = $paginator->paginate(
            //     $query,
            //     $request->query->getInt('page', 1),
            //     8,
            // );

            return $this->render('compilation/detailCompilation.html.twig', [
                'compilation' => $compilation,
                'recipes' => $recipes,
                // 'pagination' => $pagination
            ]);
        } else {
            return $this->redirectToRoute("app_home");
        }
    }

    // Permet de rajouter une recipe à une compilation
    #[Route('/compilation/edit/addRecipe/{compilation}/{recipe}', name: 'add_recipe_compilation')]
    public function addRecipeCompilation(Compilation $compilation = null, Recipe $recipe = null, EntityManagerInterface $entityManager): Response
    {
        if ($compilation && $recipe) {

            $compilation->addRecipe($recipe);

            $entityManager->persist($compilation);
            $entityManager->flush();

            return $this->redirectToRoute('detail_recipe', [
                'recipe' => $recipe->getId()
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    // Permet de retirer une recipe d'une compilation
    #[Route('/compilation/edit/removeRecipe/{compilation}/{recipe}', name: 'remove_recipe_compilation')]
    public function removeRecipeCompilation(Compilation $compilation = null, Recipe $recipe = null, EntityManagerInterface $entityManager): Response
    {
        if ($compilation && $recipe) {

            $compilation->removeRecipe($recipe);

            $entityManager->persist($compilation);
            $entityManager->flush();

            return $this->redirectToRoute("detail_compilation", [
                'compilation' => $compilation->getId()
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }
}
