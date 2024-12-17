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
        if ($user) {

            return $this->render('compilation/index.html.twig', [
                'user' => $user
            ]);
        } else {
            return $this->redirectToRoute("app_home");
        }
    }

    #[Route('/compilation/ajout/{user}', name: 'add_compilation')]
    public function addCompilation(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {

        $compilation = new Compilation();

        $form = $this->createForm(CompilationType::class, $compilation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $compilation = $form->getData();
            $compilation->setUser($user);
            $compilation->setCreationDate(new DateTime());

            $entityManager->persist($compilation);
            $entityManager->flush();

            return $this->redirectToRoute('app_compilation', [
                'user' => $user->getId()
            ]);
        }

        return $this->render('compilation/addCompilation.html.twig', [
            'addCompilationForm' => $form
        ]);
    }
}
