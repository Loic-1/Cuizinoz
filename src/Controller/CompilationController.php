<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Compilation;
use App\Form\CompilationType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompilationController extends AbstractController
{

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

    #[Route('/compilation/ajout/{user}', name: 'add_compilation')]
    public function addCompilation(User $user, Request $request, EntityManagerInterface $entityManager): Response
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
}
