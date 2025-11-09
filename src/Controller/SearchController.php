<?php

namespace App\Controller;

use App\Service\NewsApiService; // 👈 Importez le service pour l'injection
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // 👈 Importez Request pour lire le paramètre 'q'
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    // Définition de la route /recherche
    #[Route('/recherche', name: 'app_search')]

    // Injection de dépendances : Request pour les données GET, NewsApiService pour la logique API
    public function index(Request $request, NewsApiService $newsApiService): Response
    {
        // Récupère le paramètre 'q' (le terme de recherche) de l'URL, vide par défaut
        $query = $request->query->get('q', '');
        $articles = [];

        if (!empty($query)) {
            // Utilise le service pour appeler l'API
            $articles = $newsApiService->fetchArticles($query);
        }

        // ✅ CORRECTION : Rend le template de la page de résultats de recherche (search/index.html.twig)
        return $this->render('search/index.html.twig', [
            'query' => $query,
            'articles' => $articles,
        ]);
    }
}
