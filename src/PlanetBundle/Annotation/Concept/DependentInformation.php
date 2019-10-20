<?php
namespace PlanetBundle\Annotation\Concept;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation information that isn't store but count every time
 * @Target({"METHOD"})
 */
class DependentInformation
{
    private $label;

    /**
     * DependentInformation constructor.
     * @param $label
     */
    public function __construct($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }


}