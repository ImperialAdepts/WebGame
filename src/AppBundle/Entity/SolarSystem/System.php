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
     * @var string
     * @ORM\Column(name="sector_address", type="string")
     */
    private $sectorAddress;

    /**
     * @var string
     * @ORM\Column(name="local_group_coordination", type="string")
     */
    private $localGroupCoordination;

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

    /**
     * @return string
     */
    public function getSectorAddress()
    {
        return $this->sectorAddress;
    }

    /**
     * @param string $sectorAddress
     */
    public function setSectorAddress($sectorAddress)
    {
        $this->sectorAddress = $sectorAddress;
    }

    /**
     * @return string
     */
    public function getLocalGroupCoordination()
    {
        return $this->localGroupCoordination;
    }

    /**
     * @param string $localGroupCoordination
     */
    public function setLocalGroupCoordination($localGroupCoordination)
    {
        $this->localGroupCoordination = $localGroupCoordination;
    }

}

