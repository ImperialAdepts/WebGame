<?php
namespace PlanetBundle\Concept;

use PlanetBundle\UseCase;

class LiquidFuelTank extends Concept
{
    use UseCase\LandBuilding;
    use UseCase\Deposit;
    use UseCase\FuelDeposit;
}