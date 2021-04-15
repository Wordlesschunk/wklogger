<?php

namespace App\Entity;

use App\Repository\TimesheetRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TimesheetRepository::class)
 */
class Timesheet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $date;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function setStartTime($startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime()
    {
        return $this->endTime;
    }

    public function setEndTime($endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }
}
