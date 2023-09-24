<?php

namespace App\Repository;

use App\Classe\Search;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry,private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Product::class);
    }

/**
 * Requete qui me permet de récupérer les produits en fonction de la recherche de l'utilisateur
 * @return Product[]
 */
public function findWithSearch(Search $search)

{
    $quer=$this
    ->createQueryBuilder('p')
    ->select('c','p')
    ->join('p.category','c');

    if(!empty($search->categories)){
        $quer=$quer
        ->andwhere('c.id IN (:categories)')
        ->setParameter('categories',$search->categories);
    }
    if(!empty($search->string)){
        $quer=$quer
        ->andWhere('p.name LIKE :string')
        ->setParameter('string',"%{$search->string}%");

    }

    return $quer->getQuery()->getResult();
}




//    /**
//     * @param int $page
//     * @return PaginationInterface
//     */
//    public function findAllWithData(int $page): PaginationInterface
//    {
//        $data=  $this->createQueryBuilder('p')
//         //    ->andWhere('p.exampleField = :val')
//         //    ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//         //    ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;

//        $pagination= $this->paginator->paginate($data, $page,9);

//        return $pagination;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
