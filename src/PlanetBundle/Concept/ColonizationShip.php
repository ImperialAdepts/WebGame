<?php
namespace PlanetBundle\Concept;

use PlanetBundle\UseCase;

class ColonizationShip extends SpaceShip
{
    use UseCase\SpaceStructure;
    use UseCase\SpaceShipDeposit;
}