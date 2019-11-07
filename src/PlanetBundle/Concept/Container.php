<?php
namespace PlanetBundle\Concept;

use PlanetBundle\Entity\Resource\Thing;
use PlanetBundle\UseCase;

class Container extends Skeleton
{
    use UseCase\Portable;
    use UseCase\MetalMaterial;
    use UseCase\LandBuilding;
}