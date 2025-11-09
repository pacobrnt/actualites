<?php

namespace App\Repository;

use App\Entity\Favorite;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Favorite>
 *
 * @method Favorite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Favorite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Favorite[]    findAll()
 * @method Favorite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favorite::class);
    }

    /**
     * Trouve un favori par utilisateur et URL (l'identifiant unique de l'article externe).
     */
    public function findOneByUserAndUrl(User $user, string $url): ?Favorite
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.user = :user')
            ->andWhere('f.url = :url')
            ->setParameter('user', $user)
            ->setParameter('url', $url)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // Vous pouvez ajouter des méthodes personnalisées ici pour, par exemple, trouver tous les favoris
    // d'un utilisateur, mais la méthode findBy() native de Doctrine est généralement suffisante pour cela:
    // $favorites = $favoriteRepository->findBy(['user' => $user], ['likedAt' => 'DESC']);
}
