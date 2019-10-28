<?php
namespace PlanetBundle\Entity\Resource;

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
}