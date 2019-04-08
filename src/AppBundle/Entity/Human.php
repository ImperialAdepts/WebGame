<?php

namespace AppBundle\Entity;

use AppBundle\Entity\SolarSystem\Planet;
use Doctrine\ORM\Mapping as ORM;

/**
 * Human - pointer to user in another database
 *
 * @ORM\Table(name="humans")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HumanRepository")
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
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var Soul
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Soul", inversedBy="incarnations")
     * @ORM\JoinColumn(name="soul_id", referencedColumnName="id", nullable=true)
     */
    private $soul;

    /**
     * @var Planet
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SolarSystem\Planet")
     * @ORM\JoinColumn(name="planet_id", referencedColumnName="id", nullable=true)
     */
    private $planet;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return \AppBundle\Entity\Soul
     */
    public function getSoul()
    {
        return $this->soul;
    }

    /**
     * @param \AppBundle\Entity\Soul $soul
     */
    public function setSoul(Soul $soul)
    {
        $this->soul = $soul;
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

}

