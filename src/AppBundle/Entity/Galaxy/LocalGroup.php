<?php
namespace AppBundle\Entity\Galaxy;

use AppBundle\Entity\SolarSystem\Planet;
use AppBundle\Entity\SolarSystem\System;
use Tracy\Debugger;

class LocalGroup
{
    const SIZE = 5;

    /** @var Sector */
    private $sector;

    /** @var float */
    private $weight;

    /** @var System[] */
    private $builtSystems;

    /**
     * LocalGroup constructor.
     * @param Sector $sector
     * @param float $weight
     * @param array $builtSystems
     */
    public function __construct(Sector $sector, $weight, array $builtSystems = [])
    {
        $this->sector = $sector;
        $this->weight = $weight;
        $this->builtSystems = $builtSystems;
    }

    /**
     * @return System[][][]
     */
    public function getSystems() {
        $systems = [];
        for ($x = 0; $x<self::SIZE; $x++) {
            $systems[$x] = [];
            for ($y = 0; $y<self::SIZE; $y++) {
                $systems[$x][$y] = [];
                for ($z = 0; $z<self::SIZE; $z++) {
                    $systems[$x][$y][$z] = $this->getSystem(new SpaceCoordination($x, $y, $z));
                }
            }
        }
        return $systems;
    }

    /**
     * @param SpaceCoordination $coordination
     * @return System
     */
    public function getSystem(SpaceCoordination $coordination) {
        foreach ($this->builtSystems as $builtSystem) {
            if ($this->sector->getAddress()->encode() === $builtSystem->getSectorAddress()->encode() && $coordination->encode() === $builtSystem->getLocalGroupCoordination()->encode()) {
                return $builtSystem;
            }
        }
        return self::buildSystem($this, $coordination);
    }

    public static function buildSystem(LocalGroup $localGroup, SpaceCoordination $coordination)
    {
        srand($localGroup->getSector()->getAddress()->getSeed() + $coordination->getSeed() + 1);

        if ((rand(0, 10) > 1)) {
            return null;
        }

        $system = new System();
        $system->setSectorAddress($localGroup->getSector()->getAddress());
        $system->setLocalGroupCoordination($coordination);

        $sun = new Planet();
        $sun->setSystem($system);
        $sun->setGravity(rand(20, 500));
        $sun->setDiameter(rand(200, 1000000000));
        $sun->setWeight(rand(100, 100000000));
        $system->setCentralSun($sun);

        for ($i = 0; $i<rand(3, 20); $i++) {
            $satellite = new Planet();
            $satellite->setSystem($system);
            $satellite->setGravity(rand(0.1, 50));
            $satellite->setDiameter(rand(200, 10000));
            $satellite->setWeight(rand(100, 100000));
            $sun->addSatelite($satellite);

            for ($y = 0; $y<rand(0, 3); $y++) {
                $subsatellite = new Planet();
                $subsatellite->setSystem($system);
                $subsatellite->setGravity(rand(0.1, 5));
                $subsatellite->setDiameter(rand(2, 100));
                $subsatellite->setWeight(rand(10, 1000));
                $satellite->addSatelite($subsatellite);
            }
        }

        return $system;
    }

    /**
     * @return Sector
     */
    public function getSector()
    {
        return $this->sector;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }
}