<?php
namespace PlanetBundle\UseCase;

use PlanetBundle\UseCase;

trait SpaceStructure
{
    /** @var UseCase\EnergySource */
    private $energySource;

    /**
     * @return EnergySource
     */
    public function getEnergySource()
    {
        return $this->energySource;
    }

    /**
     * @param EnergySource $energySource
     */
    public function setEnergySource($energySource)
    {
        $this->energySource = $energySource;
    }

}