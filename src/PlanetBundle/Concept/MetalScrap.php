<?php
namespace PlanetBundle\Concept;

use PlanetBundle\Entity\Resource\Thing;
use PlanetBundle\UseCase;

class MetalScrap extends Concept
{
    use UseCase\Portable;
    use UseCase\MetalMaterial;
}