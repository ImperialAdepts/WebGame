<?php

namespace AppBundle\Entity\Planet;

use Doctrine\ORM\Mapping as ORM;

/**
 * Region
 *
 * @ORM\Table(name="planet_regions")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Planet\RegionRepository")
 */
class Region
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="uuid", type="integer")
	 * @ORM\Id
	 */
	private $uuid;

	/**
	 * @var Settlement
	 *
	 * @ORM\ManyToOne(targetEntity="Settlement", inversedBy="regions")
	 * @ORM\JoinColumn(name="settlement_id", referencedColumnName="id")
	 */
	private $settlement;

	/**
	 * @var BuildingProject
	 *
	 * @ORM\OneToOne(targetEntity="BuildingProject", mappedBy="region")
	 */
	private $project;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="fertility", type="float")
	 */
	private $fertility;

	/**
	 * @var OreDeposit
	 * Known deposits, not all!
	 *
	 * @ORM\OneToMany(targetEntity="OreDeposit", mappedBy="region")
	 */
	private $oreDeposits;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="height", type="integer")
	 */
	private $height;

	/**
	 * Region constructor.
	 * @param int $uuid
	 */
	public function __construct($uuid = null)
	{
		$this->uuid = $uuid;
		// generovani
		srand($uuid);
		$this->fertility = rand(0, $uuid);
		$this->height = rand(0, $uuid);
	}


	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getUuid()
	{
		return $this->uuid;
	}

	public function getId()
	{
		return $this->getUuid();
	}

	/**
	 * Set settlement
	 *
	 * @param Settlement $settlement
	 *
	 * @return Region
	 */
	public function setSettlement(Settlement $settlement)
	{
		$this->settlement = $settlement;

		return $this;
	}

	/**
	 * Get settlement
	 *
	 * @return \stdClass
	 */
	public function getSettlement()
	{
		return $this->settlement;
	}

	/**
	 * @return BuildingProject
	 */
	public function getProject()
	{
		return $this->project;
	}

	/**
	 * @param BuildingProject $project
	 */
	public function setProject($project)
	{
		$this->project = $project;
	}

	/**
	 * Set fertility
	 *
	 * @param float $fertility
	 *
	 * @return Region
	 */
	public function setFertility($fertility)
	{
		$this->fertility = $fertility;

		return $this;
	}

	/**
	 * Get fertility
	 *
	 * @return float
	 */
	public function getFertility()
	{
		return $this->fertility;
	}

	/**
	 * Set ores
	 *
	 * @param array $oreDeposits
	 *
	 * @return Region
	 */
	public function setOreDeposits($oreDeposits)
	{
		$this->oreDeposits = $oreDeposits;

		return $this;
	}

	/**
	 * Get ores
	 *
	 * @return OreDeposit[]
	 */
	public function getOreDeposits()
	{
		return $this->oreDeposits;
	}

	/**
	 * Set height
	 *
	 * @param integer $height
	 *
	 * @return Region
	 */
	public function setHeight($height)
	{
		$this->height = $height;

		return $this;
	}

	/**
	 * Get height
	 *
	 * @return int
	 */
	public function getHeight()
	{
		return $this->height;
	}
}

