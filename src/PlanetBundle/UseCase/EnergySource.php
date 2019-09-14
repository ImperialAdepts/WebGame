<?php
namespace PlanetBundle\UseCase;

use PlanetBundle\UseCase;

trait EnergySource
{
    /** @var float */
    private $kWhPerYear;

    /**
     * @return float
     */
    public function getKWhPerYear()
    {
        return $this->kWhPerYear;
    }

    /**
     * @param float $kWhPerYear
     */
    public function setKWhPerYear($kWhPerYear)
    {
        $this->kWhPerYear = $kWhPerYear;
    }


}