<?php


namespace App\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use \LogicException;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class AbstractPaginatableRepository extends ServiceEntityRepository
{
    public function paginate(QueryBuilder $queryBuilder, int $max, int $offset) : Pagerfanta
    {
        if($max <= 0) {
            throw new LogicException("$max can't be equal or less than 0");
        }

        if($offset < 0) {
            throw new LogicException("$offset can't be less than 0");
        }

        $pager = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pager->setCurrentPage(ceil($offset + 1) / $max);
        $pager->setMaxPerPage($max);

        return $pager;
    }
}