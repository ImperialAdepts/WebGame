<?php
namespace PlanetBundle\Concept;

use AppBundle\Entity\SolarSystem\Planet;
use PlanetBundle\Annotation\Concept\DependentInformation;
use PlanetBundle\UseCase;

class People extends Concept
{
    /** @var int kcal / day */
    const BASE_METABOLISM = 2500;

    /**
     * @DependentInformation()
     * @param Planet $planet
     * @return int
     */
    public function getBasalMetabolism(Planet $planet) {
        return self::BASE_METABOLISM*$planet->getGravity()*$planet->getGravity();
    }
}