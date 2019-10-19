<?php
namespace PlanetBundle\Annotation\Concept;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation property contains part of concept
 * @Target({"PROPERTY"})
 */
class Part
{
    /** @Required() */
    private $useCase;

    /**
     * Part constructor.
     * @param $useCase
     */
    public function __construct($useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * @return mixed
     */
    public function getUseCase()
    {
        return $this->useCase;
    }


}