<?php
// Fichier: src/Controller/FavoriteController.php

namespace App\Controller;

use App\Entity\Favorite;
use App\Entity\User;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/favorite')]
#[IsGranted('ROLE_USER')]
class FavoriteController extends AbstractController
{
    /**
     * Affiche les articles favoris de l'utilisateur actuel.
     */
    #[Route('/', name: 'app_favorite_index', methods: ['GET'])]
    public function index(FavoriteRepository $favoriteRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Récupère tous les favoris de l'utilisateur, triés par date de like
        $favorites = $favoriteRepository->findBy(['user' => $user], ['likedAt' => 'DESC']);

        // ✅ CORRECTION : Rend le template de la page d'index des favoris (favorite/index.html.twig)
        return $this->render('favorite/index.html.twig', [
            'favorites' => $favorites,
        ]);
    }

    /**
     * Ajoute ou supprime un article des favoris. Utilisé par un appel POST (soumis par Turbo Frame).
     */
    #[Route('/toggle', name: 'app_favorite_toggle', methods: ['POST'])]
    public function toggle(Request $request, EntityManagerInterface $entityManager, FavoriteRepository $favoriteRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // 1. Récupération des données du formulaire
        $url = $request->getPayload()->getString('url');
        $title = $request->getPayload()->getString('title');
        $image = $request->getPayload()->getString('image');
        $sourceName = $request->getPayload()->getString('sourceName');
        $csrfToken = $request->getPayload()->getString('_token');

        // Hash de l'URL pour le jeton CSRF (doit correspondre au Twig Extension)
        $urlHash = base_convert(hash('crc32b', $url), 16, 36);

        // 2. Vérification CSRF
        if (empty($url) || !$this->isCsrfTokenValid('favorite_toggle' . $urlHash, $csrfToken)) {
            return new Response('Jeton CSRF invalide ou URL manquante.', Response::HTTP_BAD_REQUEST);
        }

        // 3. Vérifie si l'article est déjà en favori
        $favorite = $favoriteRepository->findOneByUserAndUrl($user, $url);

        if ($favorite) {
            // Si trouvé, le retire (unlike)
            $entityManager->remove($favorite);
            $entityManager->flush();
            $isLiked = false;
        } else {
            // Sinon, l'ajoute (like)
            $favorite = (new Favorite())
                ->setUser($user)
                ->setUrl($url)
                ->setTitle($title)
                ->setImage($image)
                ->setSourceName($sourceName)
                ->setLikedAt(new \DateTimeImmutable());

            $entityManager->persist($favorite);
            $entityManager->flush();
            $isLiked = true;
        }

        // 4. Retourne la nouvelle version du bouton (pour mise à jour via Turbo Frame)
        $articleData = [
            'url' => $url,
            'title' => $title,
            'image' => $image,
            // 'source' est le format attendu par le template _like_button
            'source' => ['name' => $sourceName],
        ];

        // L'ID du Turbo Frame est construit comme 'like_button_frame_' + $urlHash dans le template
        return $this->render('favorite/_like_button.html.twig', [
            'article' => $articleData,
            'isLiked' => $isLiked,
            'urlHash' => $urlHash,
        ]);
    }

    // Fonction utilitaire pour le Twig Extension
    public static function isArticleLiked(FavoriteRepository $favoriteRepository, ?User $user, string $url): bool
    {
        if (!$user) {
            return false;
        }
        return (bool) $favoriteRepository->findOneByUserAndUrl($user, $url);
    }
}
