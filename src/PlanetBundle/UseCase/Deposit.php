<?php
namespace PlanetBundle\UseCase;

use PlanetBundle\Annotation\Concept\Persistent;

trait Deposit
{
    /**
     * @var float m3
     * @Persistent("float")
     */
    private $spaceCapacity;

    /**
     * @var float kg
     * @Persistent("float")
     */
    private $weightCapacity;
}