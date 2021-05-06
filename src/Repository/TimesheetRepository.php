<?php

namespace App\Repository;

use App\Entity\Timesheet;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Timesheet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Timesheet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Timesheet[]    findAll()
 * @method Timesheet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimesheetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Timesheet::class);
    }

    /**
     * @return int|mixed|string
     */
    //todo this method needs to be removed
    public function findLatestShift()
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * @return int|mixed|string|null
     *
     * @throws NonUniqueResultException
     */
    public function fetchLatestShift()
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    /**
     * @return int|mixed[]|string
     */
    public function fetchShiftsInWeek()
    {
        return $this->createQueryBuilder('t')
            ->where('t.date >= :start')
            ->andWhere('t.date <= :end')
            ->setParameter('start', Carbon::now()->startOfWeek()->format('Y-m-d'))
            ->setParameter('end', Carbon::now()->endOfWeek()->format('Y-m-d'))
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return int|mixed[]|string
     */
    public function fetchShiftsInMonth()
    {
        return $this->createQueryBuilder('t')
            ->where('t.date >= :start')
            ->andWhere('t.date <= :end')
            ->setParameter('start', Carbon::now()->startOfMonth()->format('Y-m-d'))
            ->setParameter('end', Carbon::now()->endOfMonth()->format('Y-m-d'))
            ->getQuery()
            ->getArrayResult();
    }


//    public function findByExampleField($value)
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }


    /*
    public function findOneBySomeField($value): ?Timesheet
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
