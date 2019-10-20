<?php
namespace PlanetBundle\UseCase;

use PlanetBundle\Annotation\Concept\Persistent;

trait Deposit
{
    /**
     * @var float m3
     * @Persistent("float")
     */
    private $spaceCapacity;

    /**
     * @var float kg
     * @Persistent("float")
     */
    private $weightCapacity;

    /**
     * @return float
     */
    public function getSpaceCapacity()
    {
        return $this->spaceCapacity;
    }

    /**
     * @param float $spaceCapacity
     */
    public function setSpaceCapacity($spaceCapacity)
    {
        $this->spaceCapacity = $spaceCapacity;
    }

    /**
     * @return float
     */
    public function getWeightCapacity()
    {
        return $this->weightCapacity;
    }

    /**
     * @param float $weightCapacity
     */
    public function setWeightCapacity($weightCapacity)
    {
        $this->weightCapacity = $weightCapacity;
    }

}