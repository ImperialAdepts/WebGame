<?php
namespace AppBundle\Entity\Galaxy;

use AppBundle\Entity\SolarSystem\System;

class SpaceCoordination
{
    /** @var int light years */
    const UNIT_DISTANCE = 1;

    /** @var integer */
    private $x;
    /** @var integer */
    private $y;
    /** @var integer */
    private $z;

    /**
     * SpaceSectorCoordination constructor.
     * @param int $x
     * @param int $y
     * @param int $z
     */
    public function __construct($x, $y, $z = 0)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    public static function decode($code) {
        $rawData = explode(',', $code);
        return new self($rawData[0], $rawData[1], $rawData[2]);
    }

    public function encode() {
        return "{$this->x},{$this->y},{$this->z}";
    }

    /**
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @return int
     */
    public function getZ()
    {
        return $this->z;
    }

    /**
     * @param SpaceCoordination $coordination
     * @return SpaceCoordination
     */
    public function difference(SpaceCoordination $coordination) {
        return new self($this->x - $coordination->x, $this->y - $coordination->y, $this->z - $coordination->z);
    }

    public function getSeed() {
        $seed = 0;
        for ($i = 1; $i<300; $i++) {
            $seed += ++$i*$this->getX() + ++$i*$this->getY() + ++$i*$this->getZ();
        }
        return $seed;
    }

}