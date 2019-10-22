<?php
namespace AppBundle\Entity\Galaxy;

use AppBundle\Builder\GalaxyBuilder;

class SpaceSector
{
    /** @var SpaceSectorAddress */
    private $address;
    /** @var float Sun weights */
    private $weight;

    /**
     * SpaceSector constructor.
     * @param SpaceSectorAddress $address
     * @param float $weight
     */
    public function __construct(SpaceSectorAddress $address, $weight)
    {
        $this->address = $address;
        $this->weight = $weight;
    }

    /**
     * @param SpaceSectorCoordination $subsectorCoords
     * @return SpaceSector
     */
    public function getSubSector(SpaceSectorCoordination $subsectorCoords) {
        return new SpaceSector($this->address->getSubAddress($subsectorCoords), $this->weight / pow(GalaxyBuilder::SECTOR_FACTOR, 3));
    }

    /**
     * @return SpaceSectorAddress
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