<?php
namespace PlanetBundle\Concept;

use PlanetBundle\UseCase;

class Food extends Concept
{
    use UseCase\Portable;
    use UseCase\Consumable;
}