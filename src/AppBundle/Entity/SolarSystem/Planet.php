<?php

namespace AppBundle\Entity\SolarSystem;

use AppBundle\UuidSerializer;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @var Planet[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SolarSystem\Planet", mappedBy="orbitingCenter")
     */
	private $satelites;

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
     * @var float time of one orbit, in days
     *
     * @ORM\Column(name="orbit_period", type="float", nullable=true)
     */
    private $orbitPeriod;

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

    /**
     * @var integer
     *
     * @ORM\Column(name="time_coeficient", type="integer", nullable=false)
     */
    private $timeCoefficient = 60;

    /**
     * @var integer peak count from equator to pole
     *
     * @ORM\Column(name="surface_granularity", type="integer", nullable=false)
     */
    private $surfaceGranularity = 10;

    /**
     * @var integer
     *
     * @ORM\Column(name="last_phase_counted", type="integer", nullable=true)
     */
    private $lastPhaseUpdate;

    /**
     * @var integer
     *
     * @ORM\Column(name="next_update_time", type="integer", nullable=false)
     */
    private $nextUpdateTime = 0;

    public function __construct()
    {
        $this->satelites = new ArrayCollection();
    }

    public function getName()
	{
	    if ($this->getSystem()->getCentralSun() === $this) {
	        return $this->getSystem()->getName() . " star";
        }
	    $planetNumber = 0;
	    foreach ($this->getSystem()->getCentralSun()->getSatelites() as $planet) {
	        $planetNumber++;
	        if ($planet === $this) {
                return $this->getSystem()->getName() . " " . UuidSerializer\UuidName::convertToRomanNumber($planetNumber);
            }
	        $moonNumber = 0;
            foreach ($planet->getSatelites() as $moon) {
                $moonNumber++;
                if ($moon === $this) {
                    return $this->getSystem()->getName() . " " . UuidSerializer\UuidName::convertToRomanNumber($planetNumber).chr(96+$moonNumber);
                }
            }
        }
	    return $this->getSystem()->getName() . " noname";
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
     * @return System
     */
    public function getSystem()
    {
        return $this->system;
    }

    /**
     * @param System $system
     */
    public function setSystem(System $system)
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
     * @return Planet[]
     */
    public function getSatelites()
    {
        return $this->satelites;
    }

    /**
     * @param Planet[] $satelites
     */
    public function setSatelites($satelites)
    {
        $this->satelites = $satelites;
    }

    public function addSatelite(Planet $satelite)
    {
        $this->satelites[] = $satelite;
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
    public function getOrbitPeriod()
    {
        return $this->orbitPeriod;
    }

    /**
     * @param float $orbitPeriod
     */
    public function setOrbitPeriod($orbitPeriod)
    {
        $this->orbitPeriod = $orbitPeriod;
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

    /**
     * @return integer
     */
    public function getTimeCoefficient()
    {
        return $this->timeCoefficient;
    }

    /**
     * @param integer $timeCoefficient
     */
    public function setTimeCoefficient($timeCoefficient)
    {
        $this->timeCoefficient = $timeCoefficient;
    }

    /**
     * @return int
     */
    public function getSurfaceGranularity()
    {
        return $this->surfaceGranularity;
    }

    /**
     * @param int $surfaceGranularity
     */
    public function setSurfaceGranularity($surfaceGranularity)
    {
        $this->surfaceGranularity = $surfaceGranularity;
    }

    /**
     * @return int
     */
    public function getLastPhaseUpdate()
    {
        return $this->lastPhaseUpdate;
    }

    /**
     * @param int $lastPhaseUpdate
     */
    public function setLastPhaseUpdate($lastPhaseUpdate)
    {
        $this->lastPhaseUpdate = $lastPhaseUpdate;
    }

    /**
     * @return int
     */
    public function getNextUpdateTime()
    {
        return $this->nextUpdateTime;
    }

    /**
     * @param int $nextUpdateTime
     */
    public function setNextUpdateTime($nextUpdateTime)
    {
        $this->nextUpdateTime = $nextUpdateTime;
    }

    /**
     * @param integer $distance in millions of kilometers
     * @return float in kW per m2
     */
    private function getShinePower($distance) {
        return $this->getDiameter()*$this->getWeight()/($distance*$distance);
    }

    /**
     * @param Planet shine source
     * @return float in kW per m2
     */
//    public function getShinePowerFromSource(Planet $planet) {
//        return $this->getDiameter()*$this->getWeight()/($distance*$distance);
//    }

    /**
     * @return int
     */
    public function getOrbitPhaseCount() {
        if ($this->getOrbitPeriod() === null) {
            return 6;
        }
        if ($this->getOrbitPeriod() < $this->getTimeCoefficient()*6) {
            return 6;
        }
        return 5+ceil(sqrt($this->getOrbitPeriod() / ($this->getTimeCoefficient()*6)));
    }

    public function getOrbitPhaseLengthInSec() {
        if ($this->getOrbitPeriod() !== null) {
            return floor(24 * 60 * 60 * $this->getOrbitPeriod() / $this->getOrbitPhaseCount());
        } else {
            return floor(24 * 60 * 60 * 2);
        }
    }

    public function getCoordsWidthLength($height) {
        $hemisphereSize = $this->getSurfaceGranularity();
        if (abs($height) == $hemisphereSize) {
            return 1;
        }
        return 4*($hemisphereSize-abs($height));
    }

}

