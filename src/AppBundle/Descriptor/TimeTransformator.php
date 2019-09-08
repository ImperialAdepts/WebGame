<?php
namespace AppBundle\Descriptor;

use AppBundle\Entity\SolarSystem\Planet;

class TimeTransformator
{
    // it is timestamp of 1.1.2000
    const BEGIN_OF_TIME = 946684800;

    public static function timestampToPhase(Planet $planet, $timestamp) {
        $phase = floor(self::realToGameTime($planet, $timestamp-self::BEGIN_OF_TIME) / ($planet->getOrbitPhaseLengthInSec()));
        return $phase;
    }

    public static function phaseToTimestamp(Planet $planet, $phase) {
        return self::gameToRealTime($planet, $phase * $planet->getOrbitPhaseLengthInSec()) + self::BEGIN_OF_TIME;
    }

    public static function gameToRealTime(Planet $planet, $timestamp) {
        return floor($timestamp / $planet->getTimeCoefficient());
    }

    public static function realToGameTime(Planet $planet, $timestamp) {
        return $timestamp * $planet->getTimeCoefficient();
    }

    public static function timeLengthToAge($timeLength) {
        return $timeLength / 360*24*3600;
    }
}