<?php

namespace AppBundle\UuidSerializer;

class Region
{
	const UNIQUE_UUID_LENGTH = 5;

	private $uuid;

	/** @var integer */
	private $poleDistance = 0;

	/** @var integer */
	private $eastLength = 0;

	/** @var integer */
	private $sector = 1;

	/** @var float */
	private $fertility = 1;

	/** @var integer */
	private $height = 0;

	/**
	 * Planet constructor.
	 * @param string $uuid
	 */
	public function __construct($uuid)
	{
		$this->uuid = $uuid;
		$restUuid = substr($this->uuid, Galaxy::UNIQUE_UUID_LENGTH + System::UNIQUE_UUID_LENGTH + Planet::UNIQUE_UUID_LENGTH, self::UNIQUE_UUID_LENGTH);

		$this->sector = (int)substr($restUuid, 0, 1);
		$restUuid = substr($restUuid, 1);
		$this->poleDistance = (int)substr($restUuid, 0, 3);
		$restUuid = substr($restUuid, 3);
		$this->eastLength = (int)substr($restUuid, 0, 3);
		$restUuid = substr($restUuid, 3);

		$randomizer = new SeedInterpreter($restUuid);
		$this->fertility = $randomizer->getNumber(200, 3000) / 3000;
		$this->height = $randomizer->getNumber(0, 2000);
	}

	public static function getRegionUuidByCoordinates($planetUuid, $sector, $poleDistance, $eastLength)
	{
		return $planetUuid . sprintf("%01d", $sector) . sprintf("%03d", $poleDistance) . sprintf("%03d", $eastLength);
	}

	public function getPlanetUuid()
	{
		return substr($this->uuid, 0, Galaxy::UNIQUE_UUID_LENGTH + System::UNIQUE_UUID_LENGTH + Planet::UNIQUE_UUID_LENGTH);
	}

	/**
	 * @return float
	 */
	public function getFertility()
	{
		return $this->fertility;
	}

	/**
	 * @return int
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * @return int
	 */
	public function getPoleDistance()
	{
		return $this->poleDistance;
	}

	/**
	 * @return int
	 */
	public function getEastLength()
	{
		return $this->eastLength;
	}

	/**
	 * @return int
	 */
	public function getSector()
	{
		return $this->sector;
	}

}