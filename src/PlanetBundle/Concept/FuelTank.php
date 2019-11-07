<?php
namespace PlanetBundle\Concept;

use PlanetBundle\UseCase;

class FuelTank extends ExoSkeletalHull
{
    use UseCase\LandBuilding;
    use UseCase\Deposit;
    use UseCase\FuelDeposit;
}