<?php

namespace App\Controller;

use App\Entity\Timesheet;
use App\Repository\TimesheetRepository;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="app_dashboard")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository(Timesheet::class)->findLatestShift();

        return $this->render('dashboard/dashboard.html.twig', [
            'timesheet' => $result,
        ]);
    }

    /**
     * @Route("/new", name="app_timesheet_start_shift")
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function start(EntityManagerInterface $entityManager)
    {
        $newTimesheetEntry = new Timesheet();
        $newTimesheetEntry->setDate(date('d-m-Y'));
        $newTimesheetEntry->setStartTime(new \DateTime('now'));
        $entityManager->persist($newTimesheetEntry);
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }

    /**
     * @Route("/end", name="app_timesheet_end_shift")
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function end(EntityManagerInterface $entityManager)
    {
        $currentDate = date('d-m-Y');
        $repository = $entityManager->getRepository(Timesheet::class)->findOneBy(['date' => $currentDate]);
        $repository->setEndTime(new \DateTime('now'));
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }

    /**
     * @Route("/log", name="app_shift_log")
     * @return Response
     */
    public function shiftLog(ChartBuilderInterface $chartBuilder): Response
    {
        $result = [];

        $query = $this->getDoctrine()->getRepository(Timesheet::class)
            ->createQueryBuilder('ts')
            ->getQuery();

        $result = $query->getArrayResult();

        foreach ($result as $key => $r) {
            $startTime = $r['startTime'];
            $endTime = $r['endTime'];
            $interval = $startTime->diff($endTime);
            $result[$key]['duration'] = $interval->format('%h Hours %i Minutes');
        }

        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'Sales!',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [522, 1500, 2250, 2197, 2345, 3122, 3099],
                ],
            ],
        ]);
        $chart->setOptions([
            'scales' => [
                'yAxes' => [[
                    'ticks' => [
                        'beginAtZero' => true
                    ]
                ]]
            ]
        ]);


        return $this->render('dashboard/shiftlog.html.twig', [
            'shiftHistory' => $result,
            'chart' => $chart
        ]);
    }

//    /**
//     * @Route("/wk", name="app_shift_wk")
//     * @return Response
//     */
//    public function wklog(TimesheetRepository $ts): Response
//    {
//        $start_week = date("d-m-Y",strtotime('monday this week'));
//        $end_week = date("d-m-Y",strtotime('sunday this week'));
//
//        $query = $this->getDoctrine()->getRepository(Timesheet::class)
//            ->createQueryBuilder('t')
//            ->where('t.date >= :start')
//            ->andWhere('t.date <= :end')
//            ->setParameter('start', $start_week)
//            ->setParameter('end', $end_week)
//            ->getQuery();
//
//        $result = $query->getArrayResult();
//        $totalSeconds = 0;
//
//        foreach ($result as $key => $r) {
//            $start = Carbon::parse($r['startTime']);
//            $end = Carbon::parse($r['endTime']);
//            $totalDuration = $end->diffInSeconds($start);
//            $totalSeconds += $totalDuration;
//        }
//        $totalTimeWorkedPerWk = CarbonInterval::seconds($totalSeconds)->cascade()->forHumans();
//
//        return $this->render('dashboard/dashboard.html.twig', [
//            'shiftHistory' => $result,
//        ]);
//    }

}
