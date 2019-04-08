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

	/** @var integer */
	private $orbitUuid;

	/** @var integer */
	private $diameter;

	/** @var float */
	private $gravity;

	/**
	 * Planet constructor.
	 * @param integer|null $id
	 */
	public function __construct($id = null)
	{
		$this->id = $id;
		// generovani
		$generator = new UuidSerializer\Planet($id);
		$this->gravity = $generator->getGravity();
		$this->diameter = $generator->getDiameter();
	}

	public function getName()
	{
		return UuidSerializer\UuidName::getPlanetName($this->id);
	}

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

