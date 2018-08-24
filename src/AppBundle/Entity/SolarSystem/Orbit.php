<?php

namespace AppBundle\Entity\SolarSystem;

use Doctrine\ORM\Mapping as ORM;

/**
 * Orbit
 *
 * @ORM\Table(name="solar_system_orbits", uniqueConstraints={@ORM\UniqueConstraint(name="solar_system_orbit_position_UN", columns={"radius", "offset", "orbiting_planet_uuid"})}, indexes={@ORM\Index(name="solar_system_orbits_solar_systems_FK", columns={"system_uuid"}), @ORM\Index(name="solar_system_orbits_planets_FK", columns={"orbiting_planet_uuid"})})
 * @ORM\Entity
 */
class Orbit
{
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="bigint", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $uuid;

	/** @var integer */
	private $systemUuid;

	/** @var integer */
	private $orbitingPlanetUuid;

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
		srand($uuid);
		$this->systemUuid = random_int(0, 2000);
		$this->orbitingPlanetUuid = random_int(0, 100000);
		$this->radius = $uuid * 3;
		$this->offset = $uuid * 31 % 360;
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

