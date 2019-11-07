<?php
namespace PlanetBundle\Concept;

use PlanetBundle\Entity\Resource\Thing;
use PlanetBundle\UseCase;

class MetalPlate extends Concept
{
    use UseCase\Portable;
    use UseCase\MetalMaterial;

    /**
     * @var float m2
     */
    private $surface;
    /**
     * @var int mm
     */
    private $thickness;

    /**
     * @return float
     */
    public function getSurface()
    {
        return $this->surface;
    }

    /**
     * @param float $surface
     */
    public function setSurface($surface)
    {
        $this->surface = $surface;
    }

    /**
     * @return int
     */
    public function getThickness()
    {
        return $this->thickness;
    }

    /**
     * @param int $thickness
     */
    public function setThickness($thickness)
    {
        $this->thickness = $thickness;
    }

}