<?php
namespace PlanetBundle\Concept;

use PlanetBundle\Entity\Resource\Thing;
use PlanetBundle\UseCase;

class Food extends Concept
{
    use UseCase\Portable;
    use UseCase\Consumable;
}