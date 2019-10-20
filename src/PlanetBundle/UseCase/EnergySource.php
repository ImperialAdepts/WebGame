<?php
namespace PlanetBundle\UseCase;

use AppBundle\Entity\SolarSystem\Planet;
use PlanetBundle\Annotation\Concept\DependentInformation;
use PlanetBundle\Annotation\Concept\Persistent;
use PlanetBundle\UseCase;

trait EnergySource
{
    /**
     * @var float
     * @Persistent("float")
     */
    private $kWhPerYearProduction;

    /**
     * @DependentInformation(label="power_production")
     * @return float
     */
    public function getKWhPerYearProduction()
    {
        return $this->kWhPerYearProduction;
    }

    /**
     * @param float $kWhPerYearProduction
     */
    public function setKWhPerYearProduction($kWhPerYearProduction)
    {
        $this->kWhPerYearProduction = (float) $kWhPerYearProduction;
    }

    /**
     * @DependentInformation()
     * @param Planet $planet on which planet
     * @return float
     */
    public function getKWhPerHourProduction(Planet $planet)
    {
        return $this->kWhPerYearProduction / (365*24);
    }

}