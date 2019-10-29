<?php
namespace PlanetBundle\Entity\Resource;

use PlanetBundle\Concept\Concept;

interface DepositInterface
{
    /**
     * @return ResourceDescriptor[]
     */
    public function getResourceDescriptors();

    /**
     * @param ResourceDescriptor $resourceDescriptor
     */
    public function addResourceDescriptors(ResourceDescriptor $resourceDescriptor);

    /**
     * @param string $useCase trait class
     * @return Thing[]
     */
    public function filterByUseCase($useCase);

    /**
     * @param Blueprint $blueprint
     * @return Thing[]
     */
    public function filterByBlueprint(Blueprint $blueprint);

    /**
     * @param string $concept
     * @return Thing[]
     */
    public function filterByConcept($concept);
}