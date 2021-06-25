<?php

namespace App\Services\Timesheet;

use App\Entity\Timesheet;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;

class ShiftHoursCalculator
{
    //change this value to change the time of break that is subbed
    public const breakSeconds = 1800;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param array $shifts
     * @return int
     */
    private function calculateSecondsRaw(array $shifts): int
    {
        $totalSeconds = 0;

        foreach ($shifts as $key => $r) {
            $start = Carbon::parse($r['startTime']);
            $end = Carbon::parse($r['endTime']);
            $total = $end->diffInSeconds($start);
            $subbedBreak = $total - self::breakSeconds;
            $totalSeconds += $subbedBreak;
        }

        return $totalSeconds;
    }

    /**
     * @param int $seconds
     * @return string
     */
    private function formatSecondsToText(int $seconds): string
    {
        $init = $seconds;
        $hours = floor($init / 3600);
        $minutes = floor(($init / 60) % 60);

        return sprintf('%d Hours %s Minutes', $hours, $minutes);
    }

    public function calculateComparison($firstTime, $secondTime)
    {
        if ($firstTime > $secondTime) {
            $rawTime = $firstTime - $secondTime;

            return [
                'diff' => $this->formatSecondsToText($rawTime),
                'status' => 0
            ];

        } elseif ($firstTime < $secondTime) {
            $rawTime = $secondTime - $firstTime;

            return [
                'diff' => $this->formatSecondsToText($rawTime),
                'status' => 1
            ];
        }

        throw new Exception('Error Calculating Difference');
    }

    //====================================================================

    /**
     * @param bool $formatted
     * @return string
     */
    public function HRSToday(bool $formatted)
    {
        $result[] = $this->entityManager->getRepository(Timesheet::class)->fetchShiftByToday();

        $totalSeconds = $this->calculateSecondsRaw($result);

        if ($formatted) {
            return $this->formatSecondsToText($totalSeconds);
        }
        return $totalSeconds;

    }

    /**
     * @param bool $formatted
     * @return string
     */
    public function HRSYesterday(bool $formatted)
    {
        $result[] = $this->entityManager->getRepository(Timesheet::class)->fetchYesterdayShift();

        $totalSeconds = $this->calculateSecondsRaw($result);

        if ($formatted) {
            return $this->formatSecondsToText($totalSeconds);
        }
        return $totalSeconds;
    }

    /**
     * @param bool $formatted
     * @return string
     */
    public function HRSThisWeek(bool $formatted)
    {
        $result = $this->entityManager->getRepository(Timesheet::class)->fetchShiftsInWeek();
        $totalSeconds = $this->calculateSecondsRaw($result);

        if ($formatted) {
            return $this->formatSecondsToText($totalSeconds);
        }
        return $totalSeconds;
    }

    /**
     * @param bool $formatted
     * @return string
     */
    public function HRSLastWeek(bool $formatted)
    {
        $result = $this->entityManager->getRepository(Timesheet::class)->fetchShiftsLastWeek();
        $totalSeconds = $this->calculateSecondsRaw($result);

        if ($formatted) {
            return $this->formatSecondsToText($totalSeconds);
        }
        return $totalSeconds;
    }

    /**
     * @param bool $formatted
     * @return string
     */
    public function HRSThisMonth(bool $formatted)
    {
        $result = $this->entityManager->getRepository(Timesheet::class)->fetchShiftsInMonth();
        $totalSeconds = $this->calculateSecondsRaw($result);

        if ($formatted) {
            return $this->formatSecondsToText($totalSeconds);
        }
        return $totalSeconds;
    }

    /**
     * @param bool $formatted
     * @return string
     */
    public function HRSLastMonth(bool $formatted)
    {
        $result = $this->entityManager->getRepository(Timesheet::class)->fetchShiftsLastMonth();
        $totalSeconds = $this->calculateSecondsRaw($result);

        if ($formatted) {
            return $this->formatSecondsToText($totalSeconds);
        }
        return $totalSeconds;
    }

    /**
     * @param $month
     * @return string
     */
    //todo This method needs to be hooked up
    public function hrsInMonth($month)
    {
        $result = $this->entityManager->getRepository(Timesheet::class)->fetchShiftsByMonth($month);
        $totalSeconds = 0;

        foreach ($result as $key => $r) {
            $start = Carbon::parse($r['startTime']);
            $end = Carbon::parse($r['endTime']);
            $total = $end->diffInSeconds($start);
            $subbedBreak = $total - 1800;
            $totalSeconds += $subbedBreak;
        }

        //this is to take away 30minutes for unpaid break
        $init = $totalSeconds - 1800;
        $hours = floor($init / 3600);
        $minutes = floor(($init / 60) % 60);

        return sprintf('%d Hours %s Minutes', $hours, $minutes);
    }
}
