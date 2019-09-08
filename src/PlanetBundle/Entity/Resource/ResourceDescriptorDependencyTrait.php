<?php

namespace PlanetBundle\Entity\Resource;

use Doctrine\ORM\Mapping as ORM;

trait ResourceDescriptorDependencyTrait
{
    /**
     * @var ResourceDescriptor
     *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Resource\ResourceDescriptor")
     * @ORM\JoinColumn(name="resource_descriptior_id", referencedColumnName="id", nullable=true)
     */
    private $resourceDescriptor;

    /**
     * @return ResourceDescriptor
     */
    public function getResourceDescriptor()
    {
        return $this->resourceDescriptor;
    }

    /**
     * @param ResourceDescriptor $resourceDescriptor
     */
    public function setResourceDescriptor(ResourceDescriptor $resourceDescriptor)
    {
        $this->resourceDescriptor = $resourceDescriptor;
    }

}

