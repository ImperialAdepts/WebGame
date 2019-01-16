<?php

namespace AppBundle\UuidSerializer;

class Orbit
{
	const UNIQUE_UUID_LENGTH = 5;

	private $uuid;

	/** @var integer */
	private $radius;

	/** @var integer */
	private $offset;

	/**
	 * Planet constructor.
	 * @param string $uuid
	 */
	public function __construct($uuid)
	{
		$this->uuid = $uuid;
		$randomizer = new SeedInterpreter(substr($this->uuid, Galaxy::UNIQUE_UUID_LENGTH + System::UNIQUE_UUID_LENGTH, self::UNIQUE_UUID_LENGTH));

		$this->radius = $randomizer->getNumber(0, 1000000);
		$this->offset = $randomizer->getNumber(0, 360);
	}

	public function getSystemUuid()
	{
		return substr($this->uuid, 0, Galaxy::UNIQUE_UUID_LENGTH + System::UNIQUE_UUID_LENGTH);
	}

	public function getOrbitingPlanetUuid()
	{
		return $this->getSystemUuid() . strrev(substr($this->uuid, Galaxy::UNIQUE_UUID_LENGTH + System::UNIQUE_UUID_LENGTH, self::UNIQUE_UUID_LENGTH));
	}

	public function getOrbitedPlanetUuid()
	{
		$systemGenerator = new System($this->getSystemUuid());
		$uniquUuid = substr($this->uuid, Galaxy::UNIQUE_UUID_LENGTH + System::UNIQUE_UUID_LENGTH, self::UNIQUE_UUID_LENGTH);
		// TODO: najit odpovidaji objekt, ktery je obkruzovan
		return $systemGenerator->getStarUuid();
	}

	/**
	 * @return int
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