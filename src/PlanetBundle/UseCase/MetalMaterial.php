<?php
namespace PlanetBundle\UseCase;

use PlanetBundle\Annotation\Concept\DependentInformation;
use PlanetBundle\Annotation\Concept\Persistent;
use PlanetBundle\Concept\People;
use PlanetBundle\Entity\Settlement;

trait MetalMaterial
{
    /**
     * @var string
     * @Persistent("string")
     */
    private $metalType;

    /**
     * @var string
     * @Persistent("string")
     */
    private $weightPerM2;

    /**
     * @return string
     */
    public function getMetalType()
    {
        return $this->metalType;
    }

    /**
     * @param string $metalType
     */
    public function setMetalType($metalType)
    {
        $this->metalType = $metalType;
    }

    /**
     * @return string
     */
    public function getWeightPerM2()
    {
        return $this->weightPerM2;
    }

    /**
     * @param string $weightPerM2
     */
    public function setWeightPerM2($weightPerM2)
    {
        $this->weightPerM2 = $weightPerM2;
    }

}