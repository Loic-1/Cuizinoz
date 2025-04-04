<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    // public function findRecipesByCategoryId($category_id, $orderBy = null, $order = null)
    // {
    //     $em = $this->getEntityManager();
    //     $qb = $em->createQueryBuilder();

    //     if ($orderBy && $order) {
    //         $qb->select('r')
    //             ->from('App\Entity\Recipe', 'r')
    //             ->where('r.category = :category_id')
    //             ->orderBy('r.' . $orderBy, $order)
    //             ->setParameter('category_id', $category_id)
    //         ;
    //     } else {
    //         $qb->select('r')
    //             ->from('App\Entity\Recipe', 'r')
    //             ->where('r.category = :category_id')
    //             ->setParameter('category_id', $category_id)
    //         ;
    //     }

    //     $query = $qb->getQuery();
    //     return $query->getResult();
    // }

    //    /**
    //     * @return Category[] Returns an array of Category objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Category
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
