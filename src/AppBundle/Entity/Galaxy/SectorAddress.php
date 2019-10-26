<?php
namespace AppBundle\Entity\Galaxy;

class SectorAddress
{
    public static $levelDensity = [2,2,2,2,2];

    /** @var SpaceCoordination */
    private $quadrantCoordination;

    /** @var SpaceCoordination[] */
    private $sectorCoordinations = [];

    /**
     * SectorAddress constructor.
     * @param SpaceCoordination $quadrantCoordination
     * @param SpaceCoordination[] $sectorCoordinations
     */
    public function __construct(SpaceCoordination $quadrantCoordination, array $sectorCoordinations)
    {
        $this->quadrantCoordination = $quadrantCoordination;
        $this->sectorCoordinations = $sectorCoordinations;
    }

    public static function createZeroSectorAddress() {
        $address = [];
        foreach (self::$levelDensity as $_) {
            $address[] = new SpaceCoordination(0, 0, 0);
        }
        return new self(new SpaceCoordination(0, 0, 0), $address);
    }

    public static function decode($addressCode)
    {
        $coordCodes = explode('_', $addressCode);
        $quadrant = array_pop($coordCodes);
        return new self(SpaceCoordination::decode($quadrant),
            array_map(function ($coordCode) {
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
        return new self(
            $this->quadrantCoordination,
            array_merge($this->sectorCoordinations, [
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

    /**
     * @return SectorAddress
     */
    public function getRight() {
        $reversedAddress = array_reverse($this->sectorCoordinations);
        $newCoordinations = [];
        $add = 1;
        $i = 0;
        $reverseDesities = array_reverse(self::$levelDensity);
        foreach ($reversedAddress as $originalCoordination) {
            $density = $reverseDesities[$i++];
            if ($originalCoordination->getX() + $add < $density) {
                $newCoordinations[] = new SpaceCoordination($originalCoordination->getX()+$add, $originalCoordination->getY(), $originalCoordination->getZ());
                $add = 0;
            } elseif ($originalCoordination->getX() + $add >= $density) {
                $newCoordinations[] = new SpaceCoordination(($originalCoordination->getX()+$add) % $density, $originalCoordination->getY(), $originalCoordination->getZ());
                $add = $originalCoordination->getX() + $add - $density + 1;
            }
        }
        if ($add > 0) {
            $quadrant = new SpaceCoordination($this->quadrantCoordination->getX() + $add, $this->quadrantCoordination->getY(), $this->quadrantCoordination->getZ());
        } else {
            $quadrant = $this->quadrantCoordination;
        }
        return new self($quadrant, array_reverse($newCoordinations));
    }

    /**
     * @return SectorAddress
     */
    public function getUp() {
        $reversedAddress = array_reverse($this->sectorCoordinations);
        $newCoordinations = [];
        $add = 1;
        $i = 0;
        $reverseDesities = array_reverse(self::$levelDensity);
        foreach ($reversedAddress as $originalCoordination) {
            $density = $reverseDesities[$i++];
            if ($originalCoordination->getY() + $add < $density) {
                $newCoordinations[] = new SpaceCoordination($originalCoordination->getX(), $originalCoordination->getY()+$add, $originalCoordination->getZ());
                $add = 0;
            } elseif ($originalCoordination->getY() + $add >= $density) {
                $newCoordinations[] = new SpaceCoordination($originalCoordination->getX(), ($originalCoordination->getY()+$add) % $density, $originalCoordination->getZ());
                $add = $originalCoordination->getY() + $add - $density + 1;
            }
        }
        if ($add > 0) {
            $quadrant = new SpaceCoordination($this->quadrantCoordination->getX(), $this->quadrantCoordination->getY() + $add, $this->quadrantCoordination->getZ());
        } else {
            $quadrant = $this->quadrantCoordination;
        }
        return new self($quadrant, array_reverse($newCoordinations));
    }

    public function getSeed() {
        $seed = 1123581315282;
        $i = 1;
        foreach ($this->sectorCoordinations as $coordination) {
            $seed += ++$i*$coordination->getX() + ++$i*$coordination->getY() + ++$i*$coordination->getZ();
        }
        return $seed;
    }
}