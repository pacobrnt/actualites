<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder; // <-- IMPORTANT : on importe QueryBuilder pour le typage
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Crée un QueryBuilder pour la recherche par mot-clé dans le titre, ou pour tous les articles.
     * Cette méthode retourne un QueryBuilder non exécuté, idéal pour la pagination (KnpPaginator).
     * * @param string|null $keyword Le mot-clé de recherche.
     * @return QueryBuilder
     */
    public function createAllOrSearchQuery(?string $keyword): QueryBuilder
    {
        // 1. Démarre la construction de la requête sur l'entité Article (alias 'a')
        $qb = $this->createQueryBuilder('a')
            // Triez les articles par défaut (par exemple par date de création)
            ->orderBy('a.createdAt', 'DESC');

        // 2. Vérifie si un mot-clé (keyword) a été fourni
        if (!empty($keyword)) {
            // Ajoute une clause WHERE pour la recherche de sous-chaîne dans le champ 'title'
            // Utilisation sécurisée de LIKE avec un paramètre
            $qb->andWhere($qb->expr()->like('a.title', ':keyword'))
                // Assigne la valeur :keyword, entourée des jokers '%'
                // Cela correspond à la requête SQL : WHERE a.title LIKE '%[mot-clé]%'
                ->setParameter('keyword', '%' . $keyword . '%');
        }

        // 3. Retourne le QueryBuilder. Le contrôleur/paginator l'exécutera.
        return $qb;
    }

    // Les autres méthodes (findByExampleField, findOneBySomeField) sont conservées en commentaire
    // car elles ne sont pas nécessaires pour l'implémentation de la recherche paginée.
}
