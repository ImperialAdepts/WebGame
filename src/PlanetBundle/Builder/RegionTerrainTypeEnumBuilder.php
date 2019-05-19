<?php
namespace PlanetBundle\Builder;


class RegionTerrainTypeEnumBuilder
{
    const PLAIN = 'plain';
    const DEAD = 'dead';
    const FERTILE = 'fertile';
    const TUNDRA = 'tundra';
    const ROCK = 'rock';
    const SWAMP = 'swamp';
    const WATER = 'water';
    const FROZEN = 'frozen';
    
    private $fertility = 0;
    private $temperature = 0;
    private $undergroundWater = 0;
    private $groundWater = 0;

    public function getTerrainType() {
        if ($this->groundWater > 100) return self::WATER;
        if ($this->inRange($this->fertility, 0, 10)) {
            return self::DEAD;
        }
        if ($this->inRange($this->fertility, 11, 20)) {
            return self::PLAIN;
        }
        if ($this->fertility>20) {
            return self::FERTILE;
        }
        return self::ROCK;
    }

    private function inRange($val, $min, $max) {
        return ($val >= $min && $val <= $max);
    }

    /**
     * @param int $fertility
     */
    public function setFertility($fertility)
    {
        $this->fertility = $fertility;
    }

    /**
     * @param int $temperature
     */
    public function setTemperature($temperature)
    {
        $this->temperature = $temperature;
    }

    /**
     * @param int $undergroundWater
     */
    public function setUndergroundWater($undergroundWater)
    {
        $this->undergroundWater = $undergroundWater;
    }

    /**
     * @param int $groundWater
     */
    public function setGroundWater($groundWater)
    {
        $this->groundWater = $groundWater;
    }

}