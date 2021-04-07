<?php

namespace App\Controller;

use App\Entity\Timesheet;
use Carbon\Carbon;
use Cassandra\Time;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="app_dashboard")
     */
    public function index(): Response
    {

        $query = $this->getDoctrine()
            ->getRepository(Timesheet::class)
            ->createQueryBuilder('t')
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $result = $query->getResult(Query::HYDRATE_ARRAY);


        //duration test

//        $start = Carbon::parse($result[0]['startTime']);
//        $end = Carbon::parse($result[0]['endTime']);
//        $hours = $end->diffInHours($start);
//        $seconds = $end->diffInSeconds($start);
//
//        dd($hours . ':' . $seconds);


        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'timesheet' => $result,
        ]);
    }

    /**
     * @Route("/new", name="app_new_timesheet")
     *
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    public function start(EntityManagerInterface $entityManager)
    {
        $newTimesheetEntry = new Timesheet();
        $newTimesheetEntry->setDate(new \DateTime('today'));
        $newTimesheetEntry->setStartTime(new \DateTime('now', new DateTimeZone('Europe/London')));
        $entityManager->persist($newTimesheetEntry);
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }

    /**
     * @Route("/end", name="app_end_timesheet")
     * @param EntityManagerInterface $entityManager
     */
    public function end(EntityManagerInterface $entityManager)
    {
        $currentDate = new \DateTime('today');
        $repository = $entityManager->getRepository(Timesheet::class)->findOneBy(['date' => $currentDate]);
        $repository->setEndTime(new \DateTime('now'));
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }

//
//    /**
//     * @Route("/get", name="app_get_timesheet")
//     * @param EntityManagerInterface $entityManager
//     */
//    public function timeWorked(EntityManagerInterface $entityManager)
//    {
//
//        $query = $this->getDoctrine()
//            ->getRepository(Timesheet::class)
//            ->createQueryBuilder('t')
//            ->orderBy('t.id', 'DESC')
//            ->setMaxResults(1)
//            ->getQuery();
//
//        $result = $query->getResult(Query::HYDRATE_ARRAY);
//
//
//        //duration test
//
////        $start = Carbon::parse($result[0]['startTime']);
////        $end = Carbon::parse($result[0]['endTime']);
////        $hours = $end->diffInHours($start);
////        $seconds = $end->diffInSeconds($start);
////
////        dd($hours . ':' . $seconds);
//
//
//        return $this->render('dashboard/index.html.twig', [
//            'controller_name' => 'DashboardController',
//            'timesheet' => $result,
//        ]);
//    }
}
