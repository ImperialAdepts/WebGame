<?php

namespace AppBundle\UuidSerializer;

class Galaxy
{
	const UNIQUE_UUID_LENGTH = 5;
	private $uuid;

	/**
	 * Galaxy constructor.
	 * @param $uuid
	 */
	public function __construct($uuid)
	{
		$this->uuid = $uuid;
		$randomizer = new SeedInterpreter(substr($this->uuid, 0, self::UNIQUE_UUID_LENGTH));
	}

}