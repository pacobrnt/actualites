<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $articleRepository): Response
    {
        // 4. Utilisez le Repository pour trouver les articles
        $articles = $articleRepository->findBy(
            [], // Pas de critères de recherche
            ['createdAt' => 'DESC'], // Triés par date de création décroissante
            10 // Limite de 10 articles (optionnel)
        );

        return $this->render('home/index.html.twig', [
            // 5. Passez la liste des articles au template
            'articles' => $articles,
        ]);
    }
}
