<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\StoreAccount;
use Doctrine\ORM\UnexpectedResultException;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Pagerfanta;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends AbstractPaginatableRepository
{
    private const DEFAULT_PAGE = 1;
    private const DEFAULT_CUSTOMER_QUANTITY = 5;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function findByStoreAccountAndPaginate(
        StoreAccount $store,
        int $page = self::DEFAULT_PAGE,
        int $quantity = self::DEFAULT_CUSTOMER_QUANTITY) : Pagerfanta
    {
        $queryBuilder = $this->createQueryBuilder('customer')
            ->select('customer')
            ->where('customer.storeAccount = :store')
            ->setParameter('store', $store);

        return $this->paginate($queryBuilder, $quantity, $page);
    }

    public function findFromStore(int $id, StoreAccount $store)
    {
        $queryBuilder = $this->createQueryBuilder('customer')
            ->select('customer')
            ->where('customer.storeAccount = :store')
            ->andWhere('customer.id = :id')
            ->setParameter('store', $store)
            ->setParameter('id', $id);

        try {
            $result = $queryBuilder->getQuery()->getSingleResult();
        } catch (UnexpectedResultException $e) {
            $result = null;
        }
        return $result;
    }

    // /**
    //  * @return Customer[] Returns an array of Customer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Customer
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
