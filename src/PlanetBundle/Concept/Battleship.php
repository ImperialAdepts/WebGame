<?php
namespace PlanetBundle\Concept;

use PlanetBundle\UseCase;

class Battleship extends SpaceShip
{
    use UseCase\MilitaryUnit;
    use UseCase\SpaceStructure;
    use UseCase\LongRangeWeaponSpaceMount;

    public function getFlyingRange() {
        return $this->getSublightEngine()->getMaxThrust()/$this->getWeight();
    }

    public function getWeight() {
        return $this->getSublightEngine()->getWeight();
    }

    public static function getParts()
    {
        return [
        ];
    }
}