<?php
namespace PlanetBundle\Concept;

use PlanetBundle\UseCase;

class Warehouse extends Concept
{
    use UseCase\LandBuilding;
    use UseCase\Deposit;
    use UseCase\EnergyConsumer;
}