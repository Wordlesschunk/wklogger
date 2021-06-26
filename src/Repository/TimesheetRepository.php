<?php

namespace App\Repository;

use App\Entity\Timesheet;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

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
     *
     * @throws NonUniqueResultException
     */
    public function fetchShiftByToday()
    {
        return $this->createQueryBuilder('t')
            ->where('t.date = :start')
            ->setParameter('start', Carbon::now()->format('Y-m-d'))
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return int|mixed[]|string
     *
     * @throws NonUniqueResultException
     */
    public function fetchYesterdayShift()
    {
        return $this->createQueryBuilder('t')
            ->where('t.date = :start')
            ->setParameter('start', Carbon::now()->subDay()->format('Y-m-d'))
            ->getQuery()
            ->getArrayResult();
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
    public function fetchShiftsLastWeek()
    {
        return $this->createQueryBuilder('t')
            ->where('t.date >= :start')
            ->andWhere('t.date <= :end')
            ->setParameter('start', Carbon::now()->startOfWeek()->subWeek()->format('Y-m-d'))
            ->setParameter('end', Carbon::now()->endOfWeek()->subWeek()->format('Y-m-d'))
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return int|mixed[]|string
     */
    public function fetchAllShifts()
    {
        return $this->createQueryBuilder('t')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
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

    public function fetchShiftsByMonth($month)
    {
        return $this->createQueryBuilder('t')
            ->where('t.date >= :start')
            ->andWhere('t.date <= :end')
            ->setParameter('start', Carbon::now()->startOfMonth()->setMonth($month)->format('Y-m-d'))
            ->setParameter('end', Carbon::now()->endOfMonth()->setMonth($month)->format('Y-m-d'))
            ->getQuery()
            ->getArrayResult();
    }

    public function fetchShiftsLastMonth()
    {
        return $this->createQueryBuilder('t')
            ->where('t.date >= :start')
            ->andWhere('t.date <= :end')
            ->setParameter('start', Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d'))
            ->setParameter('end', Carbon::now()->endOfMonth()->subMonth()->format('Y-m-d'))
            ->getQuery()
            ->getArrayResult();
    }
}
