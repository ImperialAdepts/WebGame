<?php
namespace PlanetBundle\UseCase;

use PlanetBundle\Annotation\Concept\Persistent;

trait LandBuilding
{
    /**
     * @var float m2
     * @Persistent("float")
     */
    private $area;

    /**
     * @return float
     */
    public function getArea()
    {
        return $this->area;
    }

}