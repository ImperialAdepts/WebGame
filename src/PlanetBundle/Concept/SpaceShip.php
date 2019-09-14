<?php
namespace PlanetBundle\Concept;

use PlanetBundle\UseCase;

class SpaceShip extends Concept
{
    use UseCase\SpaceStructure;
    use UseCase\EnergyConsumer;
    use UseCase\SpaceMountedStructurePart;
    use UseCase\LongRangeWeaponSpaceMount;

    /** @var UseCase\SublightEngine */
    private $sublightEngine;

    /**
     * @return UseCase\SublightEngine
     */
    public function getSublightEngine()
    {
        return $this->sublightEngine;
    }

    /**
     * @param UseCase\SublightEngine $sublightEngine
     */
    public function setSublightEngine($sublightEngine)
    {
        $this->sublightEngine = $sublightEngine;
    }

    public static function getParts()
    {
        return [
        ];
    }
}