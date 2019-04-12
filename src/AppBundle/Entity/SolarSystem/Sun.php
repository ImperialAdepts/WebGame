<?php

namespace AppBundle\Entity\SolarSystem;

use Doctrine\ORM\Mapping as ORM;

class Sun
{
    /**
     * @param integer $distance in millions of kilometers
     * @return float in kW per m2
     */
    public function getShinePower($distance) {
        return $this->getDiameter()*$this->getWeight()/($distance*$distance);
    }
}