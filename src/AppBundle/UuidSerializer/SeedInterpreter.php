<?php
namespace AppBundle\UuidSerializer;

class SeedInterpreter
{
	/** @var string */
	private $seed;

	private $firstRound = [];
	private $secondRound = [];
	private $firstIndex = 0;
	private $secondIndex = 0;

	/**
	 * SeedInterpreter constructor.
	 * @param string $seed
	 */
	public function __construct($seed)
	{
		$this->seed = $seed;
		$this->firstRound = str_split(md5($seed . 'kjcflsdkflsdvapoopieriytnvcxzmoiuaser'));
		$this->secondRound = str_split(strrev($seed) . $seed . md5($seed));
	}

	/**
	 * @param integer $length
	 * @return string
	 */
	public function getNewUuid($length)
	{
		$newUuid = '';
		for ($i = 0; $i < $length; $i++) {
			$this->firstIndex = $this->firstIndex++ % count($this->firstRound);
			$this->secondIndex = ($this->secondIndex + $this->getFirstNumber()) % count($this->secondRound);
			$newUuid .= $this->getSecondLetter();
		}
		return $newUuid;
	}

	/**
	 * @param integer $min
	 * @param integer $max
	 * @return integer
	 */
	public function getNumber($min, $max)
	{
		return $min + $this->getNumberFromZero($max);
	}

	/**
	 * @param integer $max
	 * @return integer
	 */
	public function getNumberFromZero($max)
	{
		$seedSize = $max / 255;
		$seed = $this->getNewUuid($seedSize);
		srand(crc32($seed));
		return rand(0, $max);
	}

	private function getFirstNumber()
	{
		return ord($this->getFirstLetter());
	}

	private function getSecondNumber()
	{
		return ord($this->getSecondLetter());
	}

	private function getFirstLetter()
	{
		return $this->firstRound[$this->firstIndex];
	}

	private function getSecondLetter()
	{
		return $this->secondRound[$this->secondIndex];
	}
}