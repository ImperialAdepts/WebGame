<?php
namespace AppBundle\Entity\Galaxy;

use AppBundle\Builder\GalaxyBuilder;
use AppBundle\Entity\SolarSystem\System;

class Sector
{
    /** @var SectorAddress */
    private $address;
    /** @var float Sun weights */
    private $weight;
    /** @var System[] */
    private $builtSystems;

    /**
     * SpaceSector constructor.
     * @param SectorAddress $address
     * @param float $weight
     * @param array $builtSystems
     */
    public function __construct(SectorAddress $address, $weight, array $builtSystems = [])
    {
        $this->address = $address;
        $this->weight = $weight;
        $this->builtSystems = $builtSystems;
    }

    /**
     * @param SpaceCoordination $subsectorCoords
     * @return Sector
     */
    public function getSubSector(SpaceCoordination $subsectorCoords) {
        return new Sector($this->address->getSubAddress($subsectorCoords), $this->weight / pow(GalaxyBuilder::SECTOR_FACTOR, 3), $this->builtSystems);
    }

    /**
     * @return LocalGroup
     */
    public function getLocalGroup() {
        return new LocalGroup($this, $this->weight / pow(GalaxyBuilder::SECTOR_FACTOR, 3), $this->builtSystems);
    }

    /**
     * @return SectorAddress
     */
    public function getAddress()
    {
        return $this->address;
    }

    public function getWeight()
    {
        return $this->weight;
    }
}