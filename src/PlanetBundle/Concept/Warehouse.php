<?php
namespace PlanetBundle\Concept;

use PlanetBundle\UseCase;

class Warehouse extends SpaceShip
{
    use UseCase\LandBuilding;
    use UseCase\Deposit;
    use UseCase\EnergyConsumer;
}