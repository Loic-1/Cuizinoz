<?php

namespace App\Controller;

use App\Entity\Tag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TagController extends AbstractController
{
    // Renvoie les compilation ayant le tag spécifié, ainsi que le tag
    #[Route('/tag/{tag}', name: 'app_tag')]
    public function index(Tag $tag = null): Response
    {
        if ($tag) {
            $compilations = $tag->getCompilations();

            $metaDescription = "Vous aimez cuisiner ? Retrouvez toutes les collections de Cuizinoz avec le tag « " . $tag->getName() . " » et découvrez de nouvelles recettes à essayer par thématique !";

            return $this->render('tag/index.html.twig', [
                'compilations' => $compilations,
                'tag' => $tag,
                'metaDescription' => $metaDescription,
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }
}
