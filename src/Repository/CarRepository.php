<?php

namespace App\Repository;

use App\Entity\Car;
use App\Data\SearchCarData;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Car|null find($id, $lockMode = null, $lockVersion = null)
 * @method Car|null findOneBy(array $criteria, array $orderBy = null)
 * @method Car[]    findAll()
 * @method Car[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarRepository extends ServiceEntityRepository
{
    public $paginator;

    public function __construct(ManagerRegistry $registry,PaginatorInterface $paginator)
    {
        parent::__construct($registry, Car::class);
        
        $this->paginator = $paginator;
    }

    /**
     * Récupère le prix minimum et maximum correspondant a une recherche
     *
     * @param SearchCarData $search
     * @return enteger[]
     */
    public function findMinMax(SearchCarData $search) {

        $result= $this->getSearchQuery($search, true)
                      ->select('Min(car.price) as min', 'MAX(car.price) as max')
                      ->getQuery()
                      ->getScalarResult();
        // dd($result);
                
        return [(int)$result[0]['min'],(int)$result[0]['max']];
    }

    /**
     * Undocumented function
     *
     * @param SearchCarData $search
     * @return QueryBuilder
     */
    private function getSearchQuery(SearchCarData $search, $ignorePrice = false) {
        $query = $this 
        ->createQueryBuilder('car')
        ->join('car.category', 'c')
        ->select('car','c');
        
        if(!empty($search->car)) {
           
            $query = $query
            ->andWhere('car.title LIKE :car')
            ->setParameter('car', "%{$search->car}%");
        }
        
        if(!empty($search->min && $ignorePrice === false)) {
            $query = $query
            ->andWhere('car.price >= :min')
            ->setParameter('min', $search->min);            
        }
        
        if(!empty($search->max && $ignorePrice === false)) {
            $query = $query
            ->andWhere('car.price <= :max')
            ->setParameter('max', $search->max);            
        }
        
        if(!empty($search->category)) {
            $query = $query
            ->andWhere('c.id IN (:category)')
            ->setParameter('category', $search->category);
            // dd($query);
        }

        return $query;
    }

    /**
     * Récupère les produits en lien avec une recherche
     *
     * @return PaginatorInterface
     */
    public function findSearch(SearchCarData $search) {

        $query = $this->getSearchQuery($search)->getQuery();
 
         return $this->paginator->paginate(
             $query,
             $search->page,
             6
         );
     }






    // /**
    //  * @return Car[] Returns an array of Car objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Car
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
