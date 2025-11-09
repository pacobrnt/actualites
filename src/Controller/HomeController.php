<?php

namespace App\Controller;

use App\Service\NewsApiService; // 👈 Importez le Service API
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    // 🚨 Injection du service API
    public function index(NewsApiService $newsApiService): Response
    {
        // Récupère les top-headlines GNews
        $articles = $newsApiService->fetchTopHeadlines('fr');

        return $this->render('home/_like_button.html.twig', [
            // Passe les articles API pour l'affichage en cartes
            'articles' => $articles,
        ]);
    }
}
