<?php
namespace AppBundle\Entity\Galaxy;

class SectorAddress
{
    public static $levelDensity = [3,3,3,5];

    /** @var SpaceCoordination */
    private $quadrantCoordination;

    /** @var SpaceCoordination[] */
    private $sectorCoordinations = [];

    /**
     * SpaceSectorAddress constructor.
     * @param SpaceCoordination[] $sectorCoordinations
     */
    public function __construct(array $sectorCoordinations)
    {
        $this->quadrantCoordination = new SpaceCoordination(0, 0, 0);
        $this->sectorCoordinations = $sectorCoordinations;
    }

    public static function createZeroSectorAddress() {
        $address = [];
        foreach (self::$levelDensity as $_) {
            $address[] = new SpaceCoordination(0, 0, 0);
        }
        return new self($address);
    }

    public static function decode($addressCode)
    {
        $coordCodes = explode('_', $addressCode);
        $quadrant = array_pop($coordCodes);
        return new self(array_map(function ($coordCode) {
            return SpaceCoordination::decode($coordCode);
        }, $coordCodes));
    }

    public function encode() {
        return $this->quadrantCoordination->encode() . '_'
            .implode('_', array_map(function ($spaceCoord) {
            return $spaceCoord->encode();
        }, $this->sectorCoordinations));
    }

    /**
     * @param SpaceCoordination $deeperCoordinations
     * @return SectorAddress
     */
    public function getSubAddress(SpaceCoordination $deeperCoordinations) {
        $size = self::$levelDensity[count($this->sectorCoordinations)];
        return new self(array_merge($this->sectorCoordinations, [
            new SpaceCoordination(
                $deeperCoordinations->getX() % $size,
                $deeperCoordinations->getY() % $size,
                $deeperCoordinations->getZ() % $size
            )
        ]));
    }

    /**
     * @return SpaceCoordination[]
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

    public function getSeed() {
        $seed = 0;
        $i = 1;
        foreach ($this->sectorCoordinations as $coordination) {
            $seed += ++$i*$coordination->getX() + ++$i*$coordination->getY() + ++$i*$coordination->getZ();
        }
        return $seed;
    }
}