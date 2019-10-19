<?php
namespace PlanetBundle\UseCase;

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
        $this->kWhPerYearProduction = $kWhPerYearProduction;
    }


}