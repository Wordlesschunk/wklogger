<?php

namespace App\Services;

use App\Entity\Timesheet;
use App\Repository\TimesheetRepository;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

class DashboardPanels
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * DashboardPanels constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @return string
     */
    public function hrsToday()
    {
        $query = $this->entityManager->getRepository(Timesheet::class)
            ->createQueryBuilder('t')
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $result = $query->getOneOrNullResult(Query::HYDRATE_ARRAY);

        $start = Carbon::parse($result['startTime']);
        $end = Carbon::parse($result['endTime']);
        $today = $start->diffInSeconds($end);

        return CarbonInterval::seconds($today)->cascade()->format('%h hours %i minutes');
    }

    /**
     * @return string
     */
    public function hrsThisWeek()
    {
        $startOfWk = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endOfWk = Carbon::now()->endOfWeek()->format('Y-m-d');

        //todo this needs to be a repo method so you can call all time entries within a week
        $query = $this->entityManager->getRepository(Timesheet::class)
            ->createQueryBuilder('t')
            ->where('t.date >= :start')
            ->andWhere('t.date <= :end')
            ->setParameter('start', $startOfWk)
            ->setParameter('end', $endOfWk)
            ->getQuery();

        $result = $query->getArrayResult();

        $totalSeconds = 0;

        foreach ($result as $key => $r) {
            $start = Carbon::parse($r['startTime']);
            $end = Carbon::parse($r['endTime']);
            $totalDuration = $end->diffInSeconds($start);
            $totalSeconds += $totalDuration;
        }

        return CarbonInterval::seconds($totalSeconds)->cascade()->forHumans(['parts' => 2]);
    }

    /**
     * @return string
     */
    public function hrsThisMonth()
    {
        $startOfMo = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMo = Carbon::now()->endOfMonth()->format('Y-m-d');
        //todo this needs to be a repo method so you can call all time entries within a week
        $query = $this->entityManager->getRepository(Timesheet::class)
            ->createQueryBuilder('t')
            ->where('t.date >= :start')
            ->andWhere('t.date <= :end')
            ->setParameter('start', $startOfMo)
            ->setParameter('end', $endOfMo)
            ->getQuery();

        $result = $query->getArrayResult();
        $totalSeconds = 0;

        foreach ($result as $key => $r) {
            $start = Carbon::parse($r['startTime']);
            $end = Carbon::parse($r['endTime']);
            $totalDuration = $end->diffInSeconds($start);
            $totalSeconds += $totalDuration;
        }

        return CarbonInterval::seconds($totalSeconds)->cascade()->forHumans();
    }

}
