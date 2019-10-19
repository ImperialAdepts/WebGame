<?php
namespace PlanetBundle\UseCase;

use PlanetBundle\Annotation\Concept\Persistent;

trait Portable
{
    /**
     * @var float m3
     * @Persistent("float")
     */
    private $space;

    /**
     * @var float kg
     * @Persistent("float")
     */
    private $weight;
}