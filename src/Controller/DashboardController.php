<?php

namespace App\Controller;

use App\Entity\Timesheet;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        $newTimesheetEntry->setStartTime(new \DateTime('now', new DateTimeZone('Europe/London')));
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
        $repository->setEndTime(new \DateTime('now'));
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }
}
