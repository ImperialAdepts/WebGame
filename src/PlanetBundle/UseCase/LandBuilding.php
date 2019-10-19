<?php
namespace PlanetBundle\UseCase;

trait LandBuilding
{
    /** @var float */
    private $m2;

    /**
     * @return float
     */
    public function getM2()
    {
        return $this->m2;
    }

}