<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Pagerfanta;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends AbstractPaginatableRepository
{
    private const DEFAULT_PRODUCT_QUANTITY = 5; //Nombre de produits par page
    private const DEFAULT_PAGE = 1; //Page à afficher

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findAndPaginate(
        int $page = self::DEFAULT_PAGE,
        int $quantity = self::DEFAULT_PRODUCT_QUANTITY) : Pagerfanta
    {
/*        if($order != 'asc' || $order != 'desc') {
            throw new LogicException("$order must be 'asc' or 'desc'");
        }*/
        $queryBuilder = $this->createQueryBuilder('product')
            ->select('product');

        return $this->paginate($queryBuilder, $quantity, $page);
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
