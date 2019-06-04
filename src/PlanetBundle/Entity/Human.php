<?php

namespace PlanetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Human
 *
 * @ORM\Table(name="humans")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\HumanRepository")
 */
class Human
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="global_human_id", type="bigint", nullable=true)
     */
    private $globalHumanId;

    /**
     * @var Peak
     *
     * @ORM\ManyToOne(targetEntity="\PlanetBundle\Entity\Peak")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="current_peak_id", referencedColumnName="id")
     * })
     */
    private $currentPeakPosition;

    /**
     * @ORM\Get("id")
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getGlobalHumanId()
    {
        return $this->globalHumanId;
    }

    /**
     * @param string $globalHumanId
     */
    public function setGlobalHumanId($globalHumanId)
    {
        $this->globalHumanId = $globalHumanId;
    }

    /**
     * @return Peak
     */
    public function getCurrentPeakPosition()
    {
        return $this->currentPeakPosition;
    }

    /**
     * @param Peak $currentPeakPosition
     */
    public function setCurrentPeakPosition(Peak $currentPeakPosition)
    {
        $this->currentPeakPosition = $currentPeakPosition;
    }

}

