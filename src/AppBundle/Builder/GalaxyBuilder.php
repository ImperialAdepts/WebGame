<?php
namespace AppBundle\Builder;

use AppBundle\Entity\Galaxy\SpaceSector;
use AppBundle\Entity\Galaxy\SpaceSectorAddress;
use AppBundle\Entity\Galaxy\SpaceSectorCoordination;
use AppBundle\Entity\SolarSystem\Planet;
use AppBundle\Entity\SolarSystem\System;

class GalaxyBuilder
{
    const SECTOR_FACTOR = 3;

    private static $firstLevelCache = null;

    public static function getFirstLevelSectors() {
        self::init();
        return self::$firstLevelCache;
    }

    public static function getSector(SpaceSectorAddress $coordAdress) {
        self::init();

        $coordAdresses = $coordAdress->getSectorCoordinations();
        $firstCoords = $coordAdresses[0];
        array_shift($coordAdresses);
        /** @var SpaceSector $sector */
        $sector = self::$firstLevelCache[$firstCoords->getX()][$firstCoords->getY()];
        foreach ($coordAdresses as $coord) {
            $sector = $sector->getSubSector($coord);
        }

        return $sector;
    }

    private static function init()
    {
        if (self::$firstLevelCache == null) {
            $firstLevelSectors = [];
            for ($x = 0; $x < self::SECTOR_FACTOR; $x++) {
                for ($y = 0; $y < self::SECTOR_FACTOR; $y++) {
                    $firstLevelSectors[$x][$y] = new SpaceSector(new SpaceSectorAddress([new SpaceSectorCoordination($x, $y)]), pow(10, 20) + rand(0, pow(10, 18)));
                }
            }
            self::$firstLevelCache = $firstLevelSectors;
        }
    }

    /**
     * @param SpaceSectorAddress $systemAddress
     * @return System
     */
    public static function buildSystem(SpaceSectorAddress $systemAddress)
    {
        srand((int)md5($systemAddress->encode()));
        $system = new System();
        $system->setSystemName(md5($systemAddress->encode()));
        $system->setSectorAddress($systemAddress->encode());

        $sun = new Planet();
        $sun->setGravity(rand(20, 500));
        $sun->setDiameter(rand(200, 1000000000));
        $sun->setWeight(rand(100, 100000000));
        $system->setCentralSun($sun);

        for ($i = 0; $i<rand(3, 20); $i++) {
            $satellite = new Planet();
            $satellite->setGravity(rand(0.1, 50));
            $satellite->setDiameter(rand(200, 10000));
            $satellite->setWeight(rand(100, 100000));
            $sun->addSatelite($satellite);

            for ($y = 0; $y<rand(0, 3); $y++) {
                $subsatellite = new Planet();
                $subsatellite->setGravity(rand(0.1, 5));
                $subsatellite->setDiameter(rand(2, 100));
                $subsatellite->setWeight(rand(10, 1000));
                $satellite->addSatelite($subsatellite);
            }
        }

        return $system;
    }
}