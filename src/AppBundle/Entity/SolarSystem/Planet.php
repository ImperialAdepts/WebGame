<?php

namespace AppBundle\Entity\SolarSystem;

use AppBundle\UuidSerializer;
use Doctrine\ORM\Mapping as ORM;
/**
 * Planet
 *
 * @ORM\Table(name="planets")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SolarSystem\PlanetRepository")
 */
class Planet
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
     * @var string docasne se pouzije jako popisek
     * TODO: doplnit enum podle typu (jupiter, asteroid, kamenna planeta, sun, ...)
     *
     * @ORM\Column(name="type", type="string", nullable=true)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SolarSystem\System")
     * @ORM\JoinColumn(name="system_id", referencedColumnName="id", nullable=false)
     */
    private $system;

	/**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SolarSystem\Planet")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="orbit_planet_center_id", referencedColumnName="id", nullable=true)
     * })
     */
	private $orbitingCenter;

    /**
     * @var float in millions of kilometers
     *
     * @ORM\Column(name="orbit_diameter", type="float", nullable=true)
     */
    private $orbitDiameter;

    /**
     * @var integer starting place on orbit, in degrees
     *
     * @ORM\Column(name="orbit_offset", type="integer", nullable=false)
     */
    private $orbitOffset = 0;

    /**
     * @var float in kilometers
     *
     * @ORM\Column(name="diameter", type="float", nullable=false)
     */
	private $diameter;

    /**
     * @var float in G
     *
     * @ORM\Column(name="gravity", type="float", nullable=false)
     */
	private $gravity;

    /**
     * @var float in GT (1 = 10^12kg)
     *
     * @ORM\Column(name="weight", type="float", nullable=false)
     */
    private $weight;

    /**
     * @var string[]
     *
     * @ORM\Column(name="database_credentials", type="json_array", nullable=true)
     */
    private $databaseCredentials;

	public function getName()
	{
		return UuidSerializer\UuidName::getPlanetName([
		    $this->id,
            $this->diameter,
        ]);
	}

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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getSystem()
    {
        return $this->system;
    }

    /**
     * @param mixed $system
     */
    public function setSystem($system)
    {
        $this->system = $system;
    }

    /**
     * @return mixed
     */
    public function getOrbitingCenter()
    {
        return $this->orbitingCenter;
    }

    /**
     * @param mixed $orbitingCenter
     */
    public function setOrbitingCenter($orbitingCenter)
    {
        $this->orbitingCenter = $orbitingCenter;
    }

    /**
     * @return float
     */
    public function getOrbitDiameter()
    {
        return $this->orbitDiameter;
    }

    /**
     * @param float $orbitDiameter
     */
    public function setOrbitDiameter($orbitDiameter)
    {
        $this->orbitDiameter = $orbitDiameter;
    }

    /**
     * @return int
     */
    public function getOrbitOffset()
    {
        return $this->orbitOffset;
    }

    /**
     * @param int $orbitOffset
     */
    public function setOrbitOffset($orbitOffset)
    {
        $this->orbitOffset = $orbitOffset;
    }

    /**
     * @return float
     */
    public function getDiameter()
    {
        return $this->diameter;
    }

    /**
     * @param float $diameter
     */
    public function setDiameter($diameter)
    {
        $this->diameter = $diameter;
    }

    /**
     * @return float
     */
    public function getGravity()
    {
        return $this->gravity;
    }

    /**
     * @param float $gravity
     */
    public function setGravity($gravity)
    {
        $this->gravity = $gravity;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return string[]
     */
    public function getDatabaseCredentials()
    {
        return $this->databaseCredentials;
    }

    /**
     * @param string[] $databaseCredentials
     */
    public function setDatabaseCredentials($databaseCredentials)
    {
        $this->databaseCredentials = $databaseCredentials;
    }

}

