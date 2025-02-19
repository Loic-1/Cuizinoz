<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findNotAdded($compilation_id)
    {

        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub;
        // sélectionner toutes les recettes d'une compilation dont l'id est passé en paramètre
        $qb->select('r')
            ->from('App\Entity\Recipe', 'r')
            ->leftJoin('r.compilations', 'c')
            ->where('c.id = :compilation_id');

        $sub = $em->createQueryBuilder();
        // sélectionner toutes les recettes qui ne SONT PAS (NOT IN) dans le résultat précédent
        // on obtient donc les recettes absentes de la compilation définie
        $sub->select('re')
            ->from('App\Entity\Recipe', 're')
            ->where($sub->expr()->notIn('re.id', $qb->getDQL()))
            // requête paramétrée (on définit :id pour $qb)
            ->setParameter('compilation_id', $compilation_id)
            // trier la liste des recettes sur le nom
            ->orderBy('re.name', 'ASC');

        // renvoyer le résultat
        $query = $sub->getQuery();
        return $query->getResult();
    }

    public function findAdded($compilation_id)
    {

        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        // sélectionner toutes les recettes d'une compilation dont l'id est passé en paramètre
        $qb->select('r')
            ->from('App\Entity\Recipe', 'r')
            ->leftJoin('r.compilations', 'c')
            ->where('c.id = :compilation_id')
            ->setParameter('compilation_id', $compilation_id)
        ;

        // renvoyer le résultat
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function findNewBestRecipes(int $limit)
    {

        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('r')
            ->from('App\Entity\Recipe', 'r')
            ->orderBy('r.note', 'DESC')
            // ->orderBy('count(r.comments)', 'DESC')
            ->setMaxResults($limit)
        ;

        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function findAllOrderedBy($orderBy, $order)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('r')
            ->from('App\Entity\Recipe', 'r')
            ->orderBy('r.' . $orderBy, $order)
        ;

        $query = $qb->getQuery();
        return $query->getResult();
    }

    //    /**
    //     * @return Recipe[] Returns an array of Recipe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Recipe
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
