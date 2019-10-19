<?php
namespace PlanetBundle\Annotation\Concept;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation property will be saved to blueprint and cannot be changed
 * @Target({"PROPERTY"})
 */
class Persistent
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