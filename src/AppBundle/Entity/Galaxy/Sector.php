<?php
namespace AppBundle\Entity\Galaxy;

use AppBundle\Builder\GalaxyBuilder;

class Sector
{
    /** @var SectorAddress */
    private $address;
    /** @var float Sun weights */
    private $weight;

    /**
     * SpaceSector constructor.
     * @param SectorAddress $address
     * @param float $weight
     */
    public function __construct(SectorAddress $address, $weight)
    {
        $this->address = $address;
        $this->weight = $weight;
    }

    /**
     * @param SpaceCoordination $subsectorCoords
     * @return Sector
     */
    public function getSubSector(SpaceCoordination $subsectorCoords) {
        return new Sector($this->address->getSubAddress($subsectorCoords), $this->weight / pow(GalaxyBuilder::SECTOR_FACTOR, 3));
    }

    /**
     * @return LocalGroup
     */
    public function getLocalGroup() {
        return new LocalGroup($this, $this->weight / pow(GalaxyBuilder::SECTOR_FACTOR, 3));
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