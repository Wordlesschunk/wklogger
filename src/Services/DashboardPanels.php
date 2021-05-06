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
        $result = $this->entityManager->getRepository(Timesheet::class)->fetchLatestShift();

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
        $result = $this->entityManager->getRepository(Timesheet::class)->fetchShiftsInWeek();
        $totalSeconds = 0;

        foreach ($result as $key => $r) {
            $start = Carbon::parse($r['startTime']);
            $end = Carbon::parse($r['endTime']);
            $totalDuration = $end->diffInSeconds($start);
            $totalSeconds += $totalDuration;
        }

        $init = $totalSeconds;
        $hours = floor($init / 3600);
        $minutes = floor(($init / 60) % 60);

        return sprintf('%d Hours %s Minutes', $hours, $minutes);
    }

    /**
     * @return string
     */
    public function hrsThisMonth()
    {
        $result = $this->entityManager->getRepository(Timesheet::class)->fetchShiftsInMonth();
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
