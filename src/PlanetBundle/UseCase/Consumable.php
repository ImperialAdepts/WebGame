<?php
namespace PlanetBundle\UseCase;

use PlanetBundle\Annotation\Concept\DependentInformation;
use PlanetBundle\Annotation\Concept\Persistent;
use PlanetBundle\Concept\People;
use PlanetBundle\Entity\Settlement;

trait Consumable
{
    /**
     * @var float kJ
     * @Persistent("float")
     */
    private $energy;

    /**
     * @DependentInformation()
     * @return float
     */
    public function getEnergy()
    {
        return $this->energy;
    }

    /**
     * @DependentInformation()
     * @param Settlement $settlement
     * @return int cycle count
     */
    public function getTimeDeposit(Settlement $settlement) {
        if ($settlement->getPeopleCount() == 0) {
            return 1000;
        }
        return $this->energy / ($settlement->getPeopleCount() * People::BASE_METABOLISM * 365);
    }

    /**
     * @param float $energy
     */
    public function setEnergy($energy)
    {
        $this->energy = $energy;
    }

}