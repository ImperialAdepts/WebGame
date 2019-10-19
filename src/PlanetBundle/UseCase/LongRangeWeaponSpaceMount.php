<?php
namespace PlanetBundle\UseCase;

use PlanetBundle\Annotation\Concept\Part;
use PlanetBundle\UseCase;

trait LongRangeWeaponSpaceMount
{
    use SpaceMountedStructurePart;

    /**
     * @var UseCase\LongRangeWeapon
     * @Part(UseCase\LongRangeWeapon::class)
     */
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