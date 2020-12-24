<?php

namespace App\Repository;

use App\Entity\Processor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Processor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Processor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Processor[]    findAll()
 * @method Processor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProcessorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Processor::class);
    }

    // /**
    //  * @return Processor[] Returns an array of Processor objects
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
    public function findOneBySomeField($value): ?Processor
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
