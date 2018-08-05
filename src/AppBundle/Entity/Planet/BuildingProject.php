<?php

namespace AppBundle\Entity\Planet;

use Doctrine\ORM\Mapping as ORM;

/**
 * BuildingProject
 *
 * @ORM\Table(name="planet_building_project")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Planet\BuildingProjectRepository")
 */
class BuildingProject
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Region
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Planet\Region", inversedBy="project")
     * @ORM\JoinColumn(name="region_uuid", referencedColumnName="uuid")
     */
    private $region;

    /**
     * @var string
     *
     * @ORM\Column(name="building", type="string", length=255)
     */
    private $building;

    /**
     * @var \AppBundle\Entity\Human
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Human")
     * @ORM\JoinColumn(name="supervisor_id", referencedColumnName="id")
     */
    private $supervisor;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;

    /**
     * @var int
     *
     * @ORM\Column(name="mandays_left", type="integer")
     */
    private $mandaysLeft;

    /**
     * @return bool
     */
    public function isDone()
    {
        return $this->getMandaysLeft() <= 0;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set region
     *
     * @param Region $region
     *
     * @return BuildingProject
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set building
     *
     * @param string $building
     *
     * @return BuildingProject
     */
    public function setBuilding($building)
    {
        $this->building = $building;

        return $this;
    }

    /**
     * Get building
     *
     * @return string
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * @return \AppBundle\Entity\Human
     */
    public function getSupervisor()
    {
        return $this->supervisor;
    }

    /**
     * @param \AppBundle\Entity\Human $supervisor
     */
    public function setSupervisor($supervisor)
    {
        $this->supervisor = $supervisor;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     *
     * @return BuildingProject
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @return int
     */
    public function getMandaysLeft()
    {
        return $this->mandaysLeft;
    }

    /**
     * @param int $mandaysLeft
     */
    public function setMandaysLeft($mandaysLeft)
    {
        $this->mandaysLeft = $mandaysLeft;
    }
}

