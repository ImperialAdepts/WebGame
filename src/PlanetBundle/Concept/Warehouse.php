<?php
namespace PlanetBundle\Concept;

use PlanetBundle\Annotation\Concept\DependentInformation;
use PlanetBundle\UseCase;

class Warehouse extends Concept
{
    use UseCase\LandBuilding;
    use UseCase\Deposit;
    use UseCase\EnergyConsumer;
}