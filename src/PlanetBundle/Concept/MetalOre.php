<?php
namespace PlanetBundle\Concept;

use PlanetBundle\Entity\Resource\Thing;
use PlanetBundle\UseCase;

class MetalOre extends Concept
{
    use UseCase\Portable;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int percent
     */
    private $quality;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * @param int $quality
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;
    }
}