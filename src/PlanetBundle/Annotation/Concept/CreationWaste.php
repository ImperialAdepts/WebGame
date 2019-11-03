<?php
namespace PlanetBundle\Annotation\Concept;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class CreationWaste
{
    /** @var int in percent */
    private $minCrapAmount = 0;
    /** @var int in percent */
    private $maxCrapAmount = 0;
}