<?php

namespace AppBundle\Entity\Human;

use AppBundle\Entity\Human;
use AppBundle\Entity\SolarSystem\Planet;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="human_events")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Human\EventRepository")
 */
class Event
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
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var string[] string => string
     *
     * @ORM\Column(name="description_data", type="json_array", nullable=true)
     */
    private $descriptionData;

    /**
     * @var integer
     *
     * @ORM\Column(name="time", type="integer", nullable=false)
     */
    private $time;

    /**
     * @var Planet
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SolarSystem\Planet")
     * @ORM\JoinColumn(name="planet_id", referencedColumnName="id", nullable=false)
     */
    private $planet;

    /**
     * @var integer
     *
     * @ORM\Column(name="planet_phase", type="integer", nullable=false)
     */
    private $planetPhase;

    /**
     * @var Human
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Human")
     * @ORM\JoinColumn(name="human_id", referencedColumnName="id", nullable=true)
     */
    private $human;

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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string[]
     */
    public function getDescriptionData()
    {
        return $this->descriptionData;
    }

    /**
     * @param string[] $descriptionData
     */
    public function setDescriptionData($descriptionData)
    {
        $this->descriptionData = $descriptionData;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param int $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return Planet
     */
    public function getPlanet()
    {
        return $this->planet;
    }

    /**
     * @param Planet $planet
     */
    public function setPlanet($planet)
    {
        $this->planet = $planet;
    }

    /**
     * @return int
     */
    public function getPlanetPhase()
    {
        return $this->planetPhase;
    }

    /**
     * @param int $planetPhase
     */
    public function setPlanetPhase($planetPhase)
    {
        $this->planetPhase = $planetPhase;
    }

    /**
     * @return Human
     */
    public function getHuman()
    {
        return $this->human;
    }

    /**
     * @param Human $human
     */
    public function setHuman(Human $human)
    {
        $this->human = $human;
    }

}
