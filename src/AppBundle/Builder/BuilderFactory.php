<?php

namespace AppBundle\Builder;

use AppBundle\Entity\Blueprint;
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
        return new RegionBuilder($this->entityManager, $what);
    }
}