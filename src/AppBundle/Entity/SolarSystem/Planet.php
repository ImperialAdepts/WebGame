<?php

namespace AppBundle\Entity\SolarSystem;

use Doctrine\ORM\Mapping as ORM;

/**
 * Planet
 *
 * @ORM\Table(name="planets")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlanetRepository")
 */
class Planet
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="uuid", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/** @var integer */
	private $orbitUuid;

	/** @var integer */
	private $diameter;

	/** @var float */
	private $gravity;

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
	 * Get id
	 *
	 * @return int
	 */
	public function getUuid()
	{
		return $this->id;
	}

	/**
	 * Planet constructor.
	 * @param integer|null $uuid
	 */
	public function __construct($uuid = null)
	{
		$this->uuid = $uuid;
		// generovani
		srand($uuid);
		$this->gravity = random_int(200, 2000) / 1000;
		$this->diameter = random_int(2000, 100000);
	}

	/**
	 * @return integer
	 */
	public function getOrbitUuid()
	{
		return $this->orbitUuid;
	}

	/**
	 * @param Orbit $orbit
	 */
	public function setOrbit($orbit)
	{
		$this->orbit = $orbit;
	}

	/**
	 * @return int
	 */
	public function getDiameter()
	{
		return $this->diameter;
	}

	/**
	 * @param int $diameter
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

}

