<?php
namespace PlanetBundle\UseCase;

use PlanetBundle\Annotation\Concept\Changeble;
use PlanetBundle\Annotation\Concept\Persistent;

trait EnergyDeposit
{
    /**
     * @var float
     * @Persistent("float")
     */
    private $maxCapacity;

    /**
     * @var float
     * @Changeble("float")
     */
    private $storedEnergy;
}