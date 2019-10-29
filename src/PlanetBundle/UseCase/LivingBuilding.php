<?php
namespace PlanetBundle\UseCase;

use PlanetBundle\Annotation\Concept\Persistent;

trait LivingBuilding
{
    /**
     * @var integer number of good lived person
     * @Persistent("integer")
     */
    private $peopleCapacity;

    /**
     * @var integer number of good lived person
     * @Persistent("integer")
     */
    private $peopleMaxCapacity;

    /**
     * @return float
     */
    public function getPeopleCapacity()
    {
        return $this->peopleCapacity;
    }

    /**
     * @param float $peopleCapacity
     */
    public function setPeopleCapacity($peopleCapacity)
    {
        $this->peopleCapacity = $peopleCapacity;
    }

    /**
     * @return int
     */
    public function getPeopleMaxCapacity()
    {
        return $this->peopleMaxCapacity;
    }

    /**
     * @param int $peopleMaxCapacity
     */
    public function setPeopleMaxCapacity($peopleMaxCapacity)
    {
        $this->peopleMaxCapacity = $peopleMaxCapacity;
    }
}