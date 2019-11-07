<?php
namespace PlanetBundle\Concept;

use PlanetBundle\Entity\Resource\Thing;
use PlanetBundle\UseCase;

class MetalTraverse extends Concept
{
    use UseCase\Portable;
    use UseCase\MetalMaterial;

    /**
     * @var int mm
     */
    private $thickness;
    /**
     * @var float m
     */
    private $length;

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

    /**
     * @return float
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param float $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

}