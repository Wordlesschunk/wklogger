<?php

namespace App\Repository;

use App\Entity\Timesheet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
    public function findLatestShift()
    {
         return $this->createQueryBuilder('t')
        ->orderBy('t.id', 'DESC')
        ->setMaxResults(1)
        ->getQuery()
        ->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * @param $startTime
     * @param $endTime
     *
     * @return mixed
     */
    public function calculateTimeBetweenHM($startTime, $endTime)
    {
            $interval = $startTime->diff($endTime);
            return $interval->format('%h Hours %i Minutes');
//            return $interval->format('%h Hours %i Minutes');
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
