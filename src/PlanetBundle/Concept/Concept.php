<?php
namespace PlanetBundle\Concept;

abstract class Concept
{
    /** @var array */
    private $context;

    public static function getParts() {
        return [];
    }

    public function injectChangeableData(array $data) {
        // TODO: doplnit pro pouziti v provozu
    }

    public function addContext($type, $object) {
        $this->context[$type] = $object;
    }

    public function getContext($type) {
        return $this->context[$type];
    }

    public function setBlueprintSettings(\PlanetBundle\Entity\Resource\Blueprint $blueprint)
    {
        foreach ($blueprint->getTraitValues() as $trait => $value) {
            $setterName = 'set'.ucfirst($trait);
            $this->$setterName($value);
        }
    }
}