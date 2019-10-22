<?php
namespace AppBundle\Entity\Galaxy;

class SpaceSectorAddress
{
    /** @var SpaceSectorCoordination[] */
    private $sectorCoordinations = [];

    /**
     * SpaceSectorAddress constructor.
     * @param SpaceSectorCoordination[] $sectorCoordinations
     */
    public function __construct(array $sectorCoordinations)
    {
        $this->sectorCoordinations = $sectorCoordinations;
    }

    public static function decode($addressCode)
    {
        $coordCodes = explode('_', $addressCode);
        return new self(array_map(function ($coordCode) {
            return SpaceSectorCoordination::decode($coordCode);
        }, $coordCodes));
    }

    public function encode() {
        return implode('_', array_map(function ($spaceCoord) {
            return $spaceCoord->encode();
        }, $this->sectorCoordinations));
    }

    /**
     * @param SpaceSectorCoordination $deeperCoordinations
     * @return SpaceSectorAddress
     */
    public function getSubAddress(SpaceSectorCoordination $deeperCoordinations) {
        return new self(array_merge($this->sectorCoordinations, [$deeperCoordinations]));
    }

    /**
     * @return SpaceSectorCoordination[]
     */
    public function getSectorCoordinations()
    {
        return $this->sectorCoordinations;
    }

    public function getSize() {
        return count($this->sectorCoordinations);
    }

    public function getLeft() {

    }
}