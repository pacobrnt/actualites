<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Form\SearchFormType; // <-- NOUVEL IMPORT : Votre formulaire de recherche
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface; // <-- NOUVEL IMPORT : Pour la pagination

#[Route('/article')]
final class ArticleController extends AbstractController
{
    #[Route(name: 'app_article_index', methods: ['GET'])]
    public function index(
        ArticleRepository $articleRepository,
        Request $request,
        PaginatorInterface $paginator // 1. Injection du service de pagination
    ): Response
    {
        // 2. Création et gestion du formulaire de recherche
        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);

        // 3. Récupération du mot-clé
        // .get('query') correspond au nom du champ dans SearchFormType
        $keyword = $searchForm->get('query')->getData();

        // 4. Obtention du QueryBuilder
        // Utilisation de la méthode que nous avons définie dans le Repository.
        // Elle gère la recherche par titre OU retourne tous les articles si le mot-clé est vide.
        $queryBuilder = $articleRepository->createAllOrSearchQuery($keyword);

        // 5. Pagination des résultats
        $pagination = $paginator->paginate(
            $queryBuilder,
            // Numéro de page (récupéré via le paramètre 'page' dans l'URL, par défaut 1)
            $request->query->getInt('page', 1),
            // Nombre d'éléments par page
            10
        );

        // ✅ CORRECTION : Rend le template de la page d'index (article/index.html.twig)
        return $this->render('article/index.html.twig', [
            'pagination' => $pagination,
            'searchForm' => $searchForm->createView(), // Passage du formulaire à la vue
        ]);
    }

    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
        // ... reste du code ...
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
