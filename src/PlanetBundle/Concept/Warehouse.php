<?php
namespace PlanetBundle\Concept;

use PlanetBundle\UseCase;

class Warehouse extends ExoSkeletalHull
{
    use UseCase\LandBuilding;
    use UseCase\Deposit;
    use UseCase\EnergyConsumer;
}