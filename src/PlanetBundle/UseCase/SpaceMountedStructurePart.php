<?php
namespace PlanetBundle\UseCase;

use PlanetBundle\Concept\ConceptToBlueprintAdapter;

trait SpaceMountedStructurePart
{
    /**
     * @return float m3
     */
    public function getVolume() {
        $volume = 0;
        /** @var EnergySource $part */
        foreach (ConceptToBlueprintAdapter::getPartsByUseCase($this, EnergySource::class) as $part) {
            $volume += 1;
        }
        return $volume;
    }
}