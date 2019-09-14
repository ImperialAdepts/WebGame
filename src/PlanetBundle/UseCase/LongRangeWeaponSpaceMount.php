<?php
namespace PlanetBundle\UseCase;

use PlanetBundle\UseCase;

trait LongRangeWeaponSpaceMount
{
    use SpaceMountedStructurePart;

    /** @var UseCase\LongRangeWeapon */
    private $longRangeWeapon;

    /**
     * @return LongRangeWeapon
     */
    public function getLongRangeWeapon()
    {
        return $this->longRangeWeapon;
    }

    /**
     * @param LongRangeWeapon $longRangeWeapon
     */
    public function setLongRangeWeapon($longRangeWeapon)
    {
        $this->longRangeWeapon = $longRangeWeapon;
    }

}