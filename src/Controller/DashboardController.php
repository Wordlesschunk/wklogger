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
        $newTimesheetEntry->setDate(new \DateTime('today'));
        $newTimesheetEntry->setStartTime(date('H:i'));
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
        $currentDate = new \DateTime('today');
        $repository = $entityManager->getRepository(Timesheet::class)->findOneBy(['date' => $currentDate]);
        $repository->setEndTime(date('H:i'));
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }

    /**
     * @Route("/log", name="app_shift_log")
     * @return Response
     */
    public function shiftLog(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository(Timesheet::class)->findAll();
        $serializer = new Serializer(array(new ObjectNormalizer()));
        $serializedData = $serializer->normalize($result);

        foreach($serializedData as $key => $result) {

            $startTime = $result['startTime'];
            $endTime = $result['endTime'];
            list($hours, $minutes) = explode(':', $startTime);
            $firstTimestamp = mktime($hours, $minutes);
            list($hours, $minutes) = explode(':', $endTime);
            $secondTimestamp = mktime($hours, $minutes);
            $firstTimestamp > $secondTimestamp?$seconds = $firstTimestamp - $secondTimestamp:$seconds = $secondTimestamp - $firstTimestamp;
            $minutes = ($seconds / 60) % 60;
            $hours = floor($seconds / (60 * 60));

            $serializedData[$key]['duration'] = "$hours Hours $minutes Mintues";
    }

        return $this->render('dashboard/shiftlog.html.twig', [
            'shiftHistory' => $serializedData,
        ]);
    }
}
