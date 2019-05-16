<?php

namespace AppBundle\Entity\SolarSystem;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Table(name="solar_systems")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SolarSystem\SolarSystemRepository")
 */
class System
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
     * @var Planet
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\SolarSystem\Planet")
     * @ORM\JoinColumn(fieldName="sun_id", nullable=true)
     */
	private $centralSun;

    /**
     * @var string
     * @ORM\Column(name="system_name", type="string")
     */
	private $systemName;

    /**
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
     * @return Planet
     */
    public function getCentralSun()
    {
        return $this->centralSun;
    }

    /**
     * @param Sun $centralSun
     */
    public function setCentralSun($centralSun)
    {
        $this->centralSun = $centralSun;
    }

    /**
     * @return string
     */
    public function getSystemName()
    {
        return $this->systemName;
    }

    /**
     * @param string $systemName
     */
    public function setSystemName($systemName)
    {
        $this->systemName = $systemName;
    }

}
