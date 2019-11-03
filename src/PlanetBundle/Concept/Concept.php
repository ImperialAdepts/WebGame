<?php
namespace PlanetBundle\Concept;

use PlanetBundle\Annotation\Concept\Persistent;
use PlanetBundle\Entity\Resource\Blueprint;

abstract class Concept
{
    /** @var array */
    private $context;

    /**
     * @var float m3
     * @Persistent("float")
     */
    private $space;

    /**
     * @var float kg
     * @Persistent("float")
     */
    private $weight;

    /**
     * @return float
     */
    public function getSpace()
    {
        return $this->space;
    }

    /**
     * @param float $space
     */
    public function setSpace($space)
    {
        $this->space = $space;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

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

    public function getBlueprint($description) {
        $blueprint = new Blueprint();
        $blueprint->setConcept(get_class($this));
        $blueprint->setDescription($description);

        return $blueprint;
    }
}