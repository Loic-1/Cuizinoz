<?php

namespace App\Repository;

use App\Entity\Recipe;
use App\Data\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{

    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Recipe::class);
        $this->paginator = $paginator;
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

    public function findSearch(SearchData $search, $user = null, bool $favorite = false): PaginationInterface
    {
        $query = $this
            ->createQueryBuilder('r')
            ->select('c', 'r')
            ->join('r.category', 'c');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('r.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->noteMin)) {
            $query = $query
                ->andWhere('r.id >= :noteMin')
                ->setParameter('noteMin', $search->noteMin);
        }

        if (!empty($search->noteMax)) {
            $query = $query
                ->andWhere('r.id <= :noteMax')
                ->setParameter('noteMax', $search->noteMax);
        }

        if (!empty($search->categories)) {
            $query = $query
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $search->categories);
        }

        if ($user) {
            $query = $query
                ->andWhere('r.user = :userId')
                ->setParameter('userId', $user->getId());
        }

        if ($favorite) {
            $query = $query
                ->andWhere('r.id IN (:userFavorites)')
                ->setParameter('userFavorites', $user->getFavoritesRecipes());
        }

        $query = $query->getQuery();
        return $this->paginator->paginate($query, $search->page, 12);
    }

    public function findBestRecipesByUserId($userId, $limit)
    {
        return $this->createQueryBuilder('r')
            ->where('r.user = :userId')
            ->setParameter('userId', $userId)
            // ->orderBy('r.note', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
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
