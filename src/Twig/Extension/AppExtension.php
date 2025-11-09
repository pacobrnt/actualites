<?php
namespace App\Twig\Extension; // CORRECTION: Le namespace doit correspondre au chemin du fichier

use App\Controller\FavoriteController;
use App\Repository\FavoriteRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private FavoriteRepository $favoriteRepository;
    private Security $security;

    public function __construct(FavoriteRepository $favoriteRepository, Security $security)
    {
        $this->favoriteRepository = $favoriteRepository;
        $this->security = $security;
    }

    public function getFunctions(): array
    {
        return [
            // Note: La fonction est bien enregistrée ici.
            new TwigFunction('is_article_liked', [$this, 'isArticleLiked']),
        ];
    }

    public function getFilters(): array
    {
        return [
            // Le filtre 'hash' qui est introuvable
            new TwigFilter('hash', [$this, 'urlHash']),
        ];
    }

    /**
     * Vérifie si l'article (via son URL) est dans les favoris de l'utilisateur connecté.
     */
    public function isArticleLiked(string $url): bool
    {
        /** @var \App\Entity\User|null $user */
        $user = $this->security->getUser();

        return FavoriteController::isArticleLiked($this->favoriteRepository, $user, $url);
    }

    /**
     * Crée un hash court pour les identifiants de cadres Turbo (Turbo Frames).
     */
    public function urlHash(string $value): string
    {
        // Hash rapide et conversion en base36 pour un ID sûr et court
        return base_convert(hash('crc32b', $value), 16, 36);
    }
}
