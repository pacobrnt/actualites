<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class NewsApiService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;
    private string $baseUrl;

    // 🚨 Les noms des arguments DOIVENT correspondre aux noms des paramètres du service.yaml
    public function __construct(HttpClientInterface $httpClient, string $gnewsApiKey, string $gnewsBaseUrl)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $gnewsApiKey;
        $this->baseUrl = $gnewsBaseUrl;
    }

    /**
     * Récupère les top-headlines (actualités générales) pour la page d'accueil (le Menu).
     */
    public function fetchTopHeadlines(string $language = 'fr', int $max = 12): array
    {
        return $this->callApi('top-headlines', [
            'lang' => $language,
            'max' => $max,
        ]);
    }

    /**
     * Récupère des articles spécifiques via recherche (utilisé par SearchController).
     */
    public function fetchArticles(string $query, string $language = 'fr'): array
    {
        // GNews utilise l'endpoint /search pour les requêtes avec 'q'
        return $this->callApi('search', [
            'q' => $query,
            'lang' => $language
        ]);
    }

    private function callApi(string $endpoint, array $params): array
    {
        try {
            $response = $this->httpClient->request('GET', $this->baseUrl . $endpoint, [
                'query' => array_merge($params, [
                    'token' => $this->apiKey, // Utilisation du token GNews
                ]),
            ]);

            $content = $response->toArray();
            return $content['articles'] ?? [];

        } catch (\Exception $e) {
            // S'il y a une erreur HTTP/Réseau, le tableau sera vide
            return [];
        }
    }
}
