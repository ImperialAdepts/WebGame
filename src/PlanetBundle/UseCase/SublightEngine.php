<?php
namespace PlanetBundle\UseCase;

use PlanetBundle\UseCase;

trait SublightEngine
{
    /** @var float */
    private $thrust;

    /**
     * @return float
     */
    public function getThrust()
    {
        return $this->thrust;
    }

    /**
     * @param float $thrust
     */
    public function setThrust($thrust)
    {
        $this->thrust = $thrust;
    }

}