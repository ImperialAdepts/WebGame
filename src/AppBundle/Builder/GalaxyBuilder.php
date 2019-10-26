<?php
namespace AppBundle\Builder;

use AppBundle\Entity\Galaxy\LocalGroup;
use AppBundle\Entity\Galaxy\Sector;
use AppBundle\Entity\Galaxy\SectorAddress;
use AppBundle\Entity\Galaxy\SpaceCoordination;
use AppBundle\Entity\SolarSystem\Planet;
use AppBundle\Entity\SolarSystem\System;

class GalaxyBuilder
{
    const SECTOR_FACTOR = 3;

    private static $firstLevelCache = null;

    public static function getSector(SectorAddress $coordAdress) {
        $coordAdresses = $coordAdress->getSectorCoordinations();
        $quadrant = $coordAdresses[0];
        array_shift($coordAdresses);
        $quadrantDistance = $quadrant->getX() + $quadrant->getY() + $quadrant->getZ();
        /** @var Sector $sector */
        $sector = new Sector(new SectorAddress($quadrant, [new SpaceCoordination($quadrant->getX(), $quadrant->getY(), $quadrant->getZ())]), $quadrantDistance * pow(10, 20) + rand(0, pow(10, 18)));
        foreach ($coordAdresses as $coord) {
            $sector = $sector->getSubSector($coord);
        }

        return $sector;
    }
}