<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
class NewsApiService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;
    private const BASE_URL = 'https://newsapi.org/v2/';

    // Symfony injecte HttpClientInterface et la valeur de NEWS_API_KEY
    public function __construct(HttpClientInterface $httpClient, string $newsApiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $newsApiKey;
    }

    /**
     * Récupère les articles d'actualités basés sur une requête.
     */
    public function fetchArticles(string $query, string $language = 'fr'): array
    {
        try {
            // Utilisation de HttpClient pour effectuer la requête GET
            $response = $this->httpClient->request('GET', self::BASE_URL . 'everything', [
                'query' => [
                    'q' => $query,
                    'apiKey' => $this->apiKey,
                    'language' => $language,
                    'sortBy' => 'publishedAt',
                    'pageSize' => 10,
                ],
            ]);

            $content = $response->toArray();

            return $content['articles'] ?? [];

        } catch (\Exception $e) {
            return [];
        }
    }
}
