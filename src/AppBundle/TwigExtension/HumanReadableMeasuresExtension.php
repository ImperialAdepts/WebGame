<?php


namespace AppBundle\TwigExtension;


use AppBundle\Entity\SolarSystem\Planet;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HumanReadableMeasuresExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('distance', [$this, 'formatDistance']),
            new TwigFilter('spaceDistance', [$this, 'formatSpaceDistance']),
            new TwigFilter('area', [$this, 'formatArea']),
            new TwigFilter('volume', [$this, 'formatVolume']),
            new TwigFilter('weight', [$this, 'formatWeight']),
            new TwigFilter('phase', [$this, 'formatPlanetTime']),
            new TwigFilter('energy', [$this, 'formatEnergy']),
        ];
    }

    /**
     * @param int $numberInMeter distance in meters
     * @return string
     */
    public function formatDistance($numberInMeter) {
        if ($numberInMeter <= 1500) {
            return round($numberInMeter, 1).' m';
        }
        $numberInKm = $numberInMeter/1000;
        if ($numberInKm <= 1500000) {
            return round($numberInKm,1).' km';
        }
        $numberInMegaKm = $numberInKm / 1000000;
        if ($numberInMegaKm <= 100000000) {
            return round($numberInMegaKm, 1).'M km';
        }
        $numberInAU = $numberInMeter / 149597870700;
        if ($numberInAU <= 100000000) {
            return round($numberInMegaKm, 1).' AU';
        }
        $numberInLightYear =  $numberInAU/173;
        return round($numberInLightYear, 1).' ly';
    }

    /**
     * @param int $numberInLy distance in meters
     * @return string
     */
    public function formatSpaceDistance($numberInLy) {
        return round($numberInLy, 2).' ly';
    }

    /**
     * @param int $numberInMeter distance in square meters
     * @return string
     */
    public function formatArea($numberInMeter) {
        return $this->formatDistance($numberInMeter).'2';
    }

    /**
     * @param int $numberInMeter distance in cubic meters
     * @return string
     */
    public function formatVolume($numberInMeter) {
        return $this->formatDistance($numberInMeter).'3';
    }

    /**
     * @param int $numberInKg weight in kilograms
     * @return string
     */
    public function formatWeight($numberInKg) {
        if ($numberInKg < 0.5) {
            return round($numberInKg*1000, 1).' g';
        }
        if ($numberInKg <= 500) {
            return round($numberInKg, 1).' kg';
        }
        $numberInTons = $numberInKg / 1000;
        if ($numberInTons <= 500000) {
            return round($numberInTons, 1).' T';
        }
        $numberInMegaTons = $numberInKg / 1000000000;
        return round($numberInMegaTons, 1).' MT';
    }

    public function formatPlanetTime(Planet $planet, $phase = null) {
        if ($phase == null) {
            $phase = $planet->getLastPhaseUpdate() + 1;
        }
        $cycle = floor($phase/$planet->getOrbitPhaseCount());
        $phaseOffset = $phase % $planet->getOrbitPhaseCount();
        return sprintf("%s of cycle %s", $phaseOffset, $cycle);
    }

    /**
     * @param int $numberInJoule
     * @return string
     */
    public function formatEnergy($numberInJoule) {
        if ($numberInJoule < 0.5) {
            return round($numberInJoule*1000, 1).' J';
        }
        if ($numberInJoule <= 500) {
            return round($numberInJoule, 1).' kJ';
        }
        $numberInMegaJoule = $numberInJoule / pow(10, 6);
        if ($numberInMegaJoule <= 500000) {
            return round($numberInMegaJoule, 1).' MJ';
        }
        $numberInGigaJoule = $numberInJoule / pow(10, 9);
        return round($numberInGigaJoule, 1).' GT';
    }
}