<?php
namespace PlanetBundle\Concept;

use PlanetBundle\Annotation\Concept\CreationSource;
use PlanetBundle\Annotation\Concept\Persistent;
use PlanetBundle\Annotation\Concept\CreationDifficulty;

/**
 * @CreationDifficulty(workHourPerTon="5")
 * @CreationSource()
 */
class Traverse extends Concept
{
    /**
     * @var string
     * @Persistent()
     */
    private $material;

    /**
     * @return string
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * @param string $material
     */
    public function setMaterial($material)
    {
        $this->material = $material;
    }
}