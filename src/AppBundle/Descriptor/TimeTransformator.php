<?php
namespace AppBundle\Descriptor;

use AppBundle\Entity\SolarSystem\Planet;

class TimeTransformator
{
    public static function timestampToPhase(Planet $planet, $timestamp) {
        if ($planet->getOrbitPeriod() == null) return 0;
        $phase = floor(self::realToGameTime($planet, $timestamp) / ($planet->getOrbitPhaseLengthInSec()));
        return $phase;
    }

    public static function phaseToTimestamp(Planet $planet, $phase) {
        if ($planet->getOrbitPeriod() == null) return 0;
        return self::gameToRealTime($planet, $phase * $planet->getOrbitPhaseLengthInSec());
    }

    public static function gameToRealTime(Planet $planet, $timestamp) {
        if ($planet->getOrbitPeriod() == null) return 0;
        return floor($timestamp / $planet->getTimeCoefficient());
    }

    public static function realToGameTime(Planet $planet, $timestamp) {
        if ($planet->getOrbitPeriod() == null) return 0;
        return $timestamp * $planet->getTimeCoefficient();
    }

    public static function timeLengthToAge($timeLength) {
        return $timeLength / 360*24*3600;
    }
}