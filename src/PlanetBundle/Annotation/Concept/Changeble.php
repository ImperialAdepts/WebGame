<?php
namespace PlanetBundle\Annotation\Concept;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation property is saved with deposit information and is changable with time and usage
 * @Target({"PROPERTY"})
 */
class Changeble
{
    /** @Required() */
    private $type;

    /**
     * Persistent constructor.
     * @param $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

}