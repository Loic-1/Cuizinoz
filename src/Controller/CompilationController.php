<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Compilation;
use App\Entity\Save;
use App\Form\CompilationType;
use App\Repository\CompilationRepository;
use App\Repository\SaveRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompilationController extends AbstractController
{
    // liste toutes les compilations du site
    #[Route('/compilations', name: 'list_compilation')]
    public function listCompilations(CompilationRepository $compilationRepository): Response
    {

        // trouve toutes les compilations stockées en BDD
        $compilations = $compilationRepository->findAll();

        return $this->render('compilation/listCompilation.html.twig', [
            'compilations' => $compilations
        ]);
    }

    // la vue permettra d'afficher les compilations sauvegardées et celles créées
    #[Route('/compilation/{user}', name: 'app_compilation')]
    public function index(User $user = null): Response
    {

        // si un User avec l'id {user} existe
        if ($user) {

            return $this->render('compilation/index.html.twig', [
                'user' => $user
            ]);
        }
        // si non
        else {

            return $this->redirectToRoute("app_home");
        }
    }

    // permet de créer une compilation
    #[Route('/compilation/ajout/{user}', name: 'create_compilation')]
    public function createCompilation(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {

        // on instancie une nouvelle compilation
        $compilation = new Compilation();

        // on définit $form à partir de App\Form\CompilationType
        $form = $this->createForm(CompilationType::class, $compilation);
        // on demande à $form d'inspecter la requête envoyée et d'appeler Symfony\Component\Form\FormInterface::submit si le bouton submit est cliqué
        $form->handleRequest($request);

        // si clic sur bouton submit et toutes les conditions remplies
        if ($form->isSubmitted() && $form->isValid()) {

            // on récupère les données entrées ds les champs
            $compilation = $form->getData();
            // on définit ce qui n'est pas demandé car illogique
            $compilation->setUser($user);
            $compilation->setCreationDate(new DateTime());

            // on prépare toutes les requêtes (on peut faire plusieurs persist())
            $entityManager->persist($compilation);
            // on envoie tout d'un coup
            $entityManager->flush();

            // puis on redirige vers la liste des collection/compilations
            return $this->redirectToRoute('app_compilation', [
                'user' => $user->getId()
            ]);
        }

        // on envoie le $form
        return $this->render('compilation/addCompilation.html.twig', [
            'addCompilationForm' => $form
        ]);
    }

    // permet de sauvegarder une compilation
    #[Route('/compilation/save/{user}/{compilation}', name: 'add_compilation')]
    public function addSave(Compilation $compilation, User $user, EntityManagerInterface $entityManager, SaveRepository $saveRepository): Response
    {

        if (count($saveRepository->isUnique($compilation->getId())) == 0) {

            // création d'une nouvelle instance de Save, $save
            $save = new Save();
            // on définit les attributs
            $save->setUser($user);
            $save->setCompilation($compilation);
            $save->setRegisterDate(new DateTime());

            // on prépare le push
            $entityManager->persist($save);
            // on push
            $entityManager->flush();

            return $this->redirectToRoute("list_compilation");
        }

        return $this->render("compilation/index.html.twig", [
            "user" => $user
        ]);
    }

    // permet de sauvegarder une compilation
    #[Route('/compilation/remove/{user}/{save}', name: 'remove_compilation')]
    public function removeSave(Save $save, User $user, EntityManagerInterface $entityManager): Response
    {

        $user->removeSave($save);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute("app_compilation", [
            "user" => $user->getId()
        ]);
    }

    // permet d'afficher le détail d'une compilation
    #[Route('/compilation/detail/{compilation}', name: 'detail_compilation')]
    public function detailCompilation(Compilation $compilation): Response
    {
        if ($compilation) {

            return $this->render('compilation/detailCompilation.html.twig', [
                'compilation' => $compilation
            ]);
        } else {
            return $this->redirectToRoute("app_home");
        }
    }
}
