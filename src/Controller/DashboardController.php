<?php

namespace App\Controller;

use App\Entity\Timesheet;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use function Symfony\Component\String\s;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="app_dashboard")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository(Timesheet::class)->findLatestShift();

        return $this->render('dashboard/index.html.twig', [
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
    public function shiftLog(): Response
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

        return $this->render('dashboard/shiftlog.html.twig', [
            'shiftHistory' => $result,
        ]);
    }
}
