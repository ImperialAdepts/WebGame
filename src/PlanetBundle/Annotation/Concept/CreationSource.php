<?php
namespace PlanetBundle\Annotation\Concept;


/**
 * @Annotation
 * @Target({"CLASS"})
 */
class CreationSource
{
    /** @var string Concept */
    private $sourceConcept = 0;
}