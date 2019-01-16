<?php

namespace AppBundle\Entity\SolarSystem;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\UuidSerializer;

/**
 * Orbit
 *
 * @ORM\Table(name="solar_system_orbits",
 *	uniqueConstraints={
 * 	},
 *  indexes={
 *  })
 * @ORM\Entity
 */
class Orbit
{
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="string", length=20, nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="NONE")
	 */
	private $uuid;

	/** @var string */
	private $systemUuid;

	/** @var string */
	private $orbitingPlanetUuid;

	/** @var string */
	private $orbitedPlanetUuid;

	/** @var integer */
	private $radius;

	/** @var integer */
	private $offset;

	/**
	 * Orbit constructor.
	 * @param int $uuid
	 */
	public function __construct($uuid = null)
	{
		$this->uuid = $uuid;
		// generovani
		$generator = new UuidSerializer\Orbit($uuid);
		$this->systemUuid = $generator->getSystemUuid();
		$this->orbitingPlanetUuid = $generator->getOrbitingPlanetUuid();
		$this->orbitedPlanetUuid = $generator->getOrbitedPlanetUuid();
		$this->radius = $generator->getRadius();
		$this->offset = $generator->getOffset();
	}

	/**
	 * @return int
	 */
	public function getUuid()
	{
		return $this->uuid;
	}

	/**
	 * @return int
	 */
	public function getSystemUuid()
	{
		return $this->systemUuid;
	}

	/**
	 * @return int
	 */
	public function getOrbitingPlanetUuid()
	{
		return $this->orbitingPlanetUuid;
	}

	/**
	 * @ORM\Get("radius")
	 */
	public function getRadius()
	{
		return $this->radius;
	}

	/**
	 * @return int
	 */
	public function getOffset()
	{
		return $this->offset;
	}


}

