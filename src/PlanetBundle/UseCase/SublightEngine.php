<?php
namespace PlanetBundle\UseCase;

use PlanetBundle\Annotation\Concept\Persistent;

trait SublightEngine
{
    /**
     * @var float N/s
     * @Persistent("float")
     */
    private $maxThrust;

    /**
     * @return float
     */
    public function getMaxThrust()
    {
        return $this->maxThrust;
    }

    /**
     * @param float $maxThrust
     */
    public function setMaxThrust($maxThrust)
    {
        $this->maxThrust = $maxThrust;
    }

}