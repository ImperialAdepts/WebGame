<?php

namespace AppBundle\Builder;

use PlanetBundle\Builder\RegionBuilder;
use PlanetBundle\Entity\Blueprint;
use Doctrine\ORM\EntityManager;

class BuilderFactory
{
    /** @var EntityManager */
    private $entityManager;

    /**
     * BuilderFactory constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Blueprint $what
     * @return RegionBuilder
     */
    public function createRegionBuilder(Blueprint $what) {
        return new RegionBuilder($what, $what->getResourceRequirements(), $what->getUseCaseRequirements());
    }
}