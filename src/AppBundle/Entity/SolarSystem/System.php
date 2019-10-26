<?php

namespace AppBundle\Entity\SolarSystem;

use AppBundle\Entity\Galaxy\SectorAddress;
use AppBundle\Entity\Galaxy\SpaceCoordination;
use AppBundle\UuidSerializer\UuidName;
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
     * @ORM\Column(name="system_name", type="string", nullable=true)
     */
	private $name;

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
     * @param Planet $centralSun
     */
    public function setCentralSun(Planet $centralSun)
    {
        $this->centralSun = $centralSun;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if (empty($this->name)) {
            return UuidName::getPlanetName([
                $this->sectorAddress,
                $this->localGroupCoordination,
                $this->centralSun->getWeight(),
            ]);
        }
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
     * @return SectorAddress
     */
    public function getSectorAddress()
    {
        return SectorAddress::decode($this->sectorAddress);
    }

    /**
     * @param SectorAddress $sectorAddress
     */
    public function setSectorAddress(SectorAddress $sectorAddress)
    {
        $this->sectorAddress = $sectorAddress->encode();
    }

    /**
     * @return SpaceCoordination
     */
    public function getLocalGroupCoordination()
    {
        return SpaceCoordination::decode($this->localGroupCoordination);
    }

    /**
     * @param SpaceCoordination $localGroupCoordination
     */
    public function setLocalGroupCoordination(SpaceCoordination $localGroupCoordination)
    {
        $this->localGroupCoordination = $localGroupCoordination->encode();
    }

}

