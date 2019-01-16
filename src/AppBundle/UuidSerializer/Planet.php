<?php

namespace AppBundle\UuidSerializer;

class Planet
{
	const UNIQUE_UUID_LENGTH = 5;

	private $uuid;

	/** @var integer */
	private $diameter;

	/** @var float */
	private $gravity;

	/**
	 * Planet constructor.
	 * @param string $uuid
	 */
	public function __construct($uuid)
	{
		$this->uuid = $uuid;
		$randomizer = new SeedInterpreter(substr($this->uuid, 5, 15));

		$this->diameter = $randomizer->getNumber(200, 100000);
		$this->gravity = $randomizer->getNumber(200, 2000) / 1000;
	}

	public function getOrbitUuid()
	{
		$galaxyAndSystemUuidLength = 15;
		$galaxyAndSystemPart = substr($this->uuid, 0, $galaxyAndSystemUuidLength);
		$planetPart = strrev(substr($this->uuid, $galaxyAndSystemUuidLength, $galaxyAndSystemUuidLength + self::UNIQUE_UUID_LENGTH));
		return $galaxyAndSystemPart . $planetPart;
	}

	/**
	 * @return int
	 */
	public function getDiameter()
	{
		return $this->diameter;
	}

	/**
	 * @return float
	 */
	public function getGravity()
	{
		return $this->gravity;
	}


}