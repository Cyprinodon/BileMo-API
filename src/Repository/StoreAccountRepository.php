<?php

namespace App\Repository;

use App\Entity\StoreAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StoreAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method StoreAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method StoreAccount[]    findAll()
 * @method StoreAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoreAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoreAccount::class);
    }

    // /**
    //  * @return StoreAccount[] Returns an array of StoreAccount objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StoreAccount
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
