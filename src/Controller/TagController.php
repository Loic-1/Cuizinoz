<?php

namespace App\Controller;

use App\Entity\Tag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TagController extends AbstractController
{
    #[Route('/tag/{tag}', name: 'app_tag')]
    public function index(Tag $tag = null): Response
    {
        if ($tag) {
            $compilations = $tag->getCompilations();

            return $this->render('tag/index.html.twig', [
                'compilations' => $compilations
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }
}
