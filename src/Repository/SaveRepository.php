<?php

namespace App\Repository;

use App\Entity\Save;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Save>
 */
class SaveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Save::class);
    }

    public function isUnique($compilation_id) {

        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('s')
        ->from('App\Entity\Save', 's')
        ->where('s.compilation = :compilation_id')
        ->setParameter('compilation_id', $compilation_id)
        ;

        $query = $qb->getQuery();
        return $query->getResult();
    }

    //    /**
    //     * @return Save[] Returns an array of Save objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Save
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
