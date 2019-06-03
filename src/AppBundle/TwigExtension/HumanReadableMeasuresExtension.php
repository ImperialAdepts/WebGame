<?php


namespace AppBundle\TwigExtension;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HumanReadableMeasuresExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('distance', [$this, 'formatDistance']),
            new TwigFilter('area', [$this, 'formatArea']),
            new TwigFilter('volume', [$this, 'formatVolume']),
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

}