<?php
namespace PlanetBundle\Concept;

use PlanetBundle\Annotation\Concept\Part;
use PlanetBundle\UseCase;

class BurnerGenerator extends Concept
{
    use UseCase\EnergySource;

    /**
     * @var UseCase\FuelDeposit
     * @Part(FuelDeposit::class)
     */
    private $fuelDeposit;
}