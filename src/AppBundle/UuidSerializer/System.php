<?php

namespace AppBundle\UuidSerializer;

class System
{
	const UNIQUE_UUID_LENGTH = 10;

	private $uuid;

	/** @var string */
	private $starUuid;
	/** @var array orbitUuid => planetUuid */
	private $planets = [];

	/**
	 * Planet constructor.
	 * @param string $uuid
	 */
	public function __construct($uuid)
	{
		$this->uuid = $uuid;
		$randomizer = new SeedInterpreter(substr($this->uuid, Galaxy::UNIQUE_UUID_LENGTH, self::UNIQUE_UUID_LENGTH));

		$this->starUuid = $this->getGalaxyUuid() . $randomizer->getNewUuid(10);

		$planetCount = $randomizer->getNumber(3, 300);
		foreach (range(0, $planetCount) as $i) {
			$newUuid = $randomizer->getNewUuid(5);
			$planetUuid = $this->uuid . $newUuid;
			$orbitUuid = $this->uuid . strrev($newUuid);
			$this->planets[$orbitUuid] = $planetUuid;
		}
	}

	public function getPlanetUuids()
	{
		return $this->planets;
	}

	public function getGalaxyUuid()
	{
		return substr($this->uuid, 0, Galaxy::UNIQUE_UUID_LENGTH);
	}

	public function getStarUuid()
	{
		return $this->starUuid;
	}
}