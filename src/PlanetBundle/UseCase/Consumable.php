<?php
namespace PlanetBundle\UseCase;

use PlanetBundle\Annotation\Concept\Persistent;

trait Consumable
{
    /**
     * @var float kJ
     * @Persistent("float")
     */
    private $energy;
}