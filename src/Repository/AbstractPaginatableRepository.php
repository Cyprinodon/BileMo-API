<?php


namespace App\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use \LogicException;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

abstract class AbstractPaginatableRepository extends ServiceEntityRepository
{
    public function paginate(QueryBuilder $queryBuilder, int $max, int $offset) : Pagerfanta
    {
        if($max <= 0) {
            throw new LogicException("$max can't be equal or less than 0");
        }

        if($offset < 1) {
            throw new LogicException("$offset can't be less than 1");
        }

        $pager = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pager->setCurrentPage((int)ceil($offset / $max));

        $pager->setMaxPerPage($max);

        return $pager;
    }
}