<?php

namespace App\Repository;

use App\Entity\Favorite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Select;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Favorite>
 */
class FavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favorite::class);
    }

    public function findFavorites($user_id) {
        
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('f')
        ->from('App\Entity\Favorite', 'f')
        ->where('f.user = :user_id')
        ->orderBy('f.registerDate', 'DESC')
        ->setParameter('user_id', $user_id)
        ;

        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function isUnique($recipe_id) {

        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('f')
        ->from('App\Entity\Favorite', 'f')
        ->where('f.recipe = :recipe_id')
        ->setParameter('recipe_id', $recipe_id)
        ;

        $query = $qb->getQuery();
        return $query->getResult();
    }

    //    /**
    //     * @return Favorite[] Returns an array of Favorite objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Favorite
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
