<?php
namespace PlanetBundle\Concept;

use PlanetBundle\Annotation\Concept\InstallationDifficulty;
use PlanetBundle\Annotation\Concept\Part;
use PlanetBundle\UseCase;

class BurnerGenerator extends Concept
{
    use UseCase\EnergySource;

    /**
     * @var UseCase\FuelDeposit
     * @Part(FuelDeposit::class)
     * @InstallationDifficulty(workHours=5, workHourPerCubicMeter="0.5", workHourPerTon="3", parallelismLimit=3)
     */
    private $fuelDeposit;
}