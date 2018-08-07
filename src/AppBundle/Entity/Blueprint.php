<?php

namespace AppBundle\Entity;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * Blueprint
 *
 * @ORM\Table(name="blueprints")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BlueprintRepository")
 */
class Blueprint
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", unique=true, length=255)
     */
    private $description;

    /**
     * @var \stdClass
     *
     * @ORM\Column(name="resource_descriptor", type="string", length=255)
     */
    private $resourceDescriptor;

    /**
     * @var float in kg
     *
     * @ORM\Column(name="weight", type="float")
     */
    private $weight;

    /**
     * @var float in m3
     *
     * @ORM\Column(name="space", type="float")
     */
    private $space;

    /**
     * @var string[] resource_descriptor => count
     *
     * @ORM\Column(name="requirements", type="json_array")
     */
    private $requirements;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $description
     *
     * @return Blueprint
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set resource
     *
     * @param \stdClass $resourceDescriptor
     *
     * @return Blueprint
     */
    public function setResourceDescriptor($resourceDescriptor)
    {
        $this->resourceDescriptor = $resourceDescriptor;

        return $this;
    }

    /**
     * Get resource
     *
     * @return \stdClass
     */
    public function getResourceDescriptor()
    {
        return $this->resourceDescriptor;
    }

    /**
     * Get mandays
     *
     * @return int
     */
    public function getMandays()
    {
        return $this->requirements[ResourceDescriptorEnum::MANDAY];
    }

    /**
     * @param float $weight
     *
     * @return Blueprint
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @return float
     */
    public function getSpace()
    {
        return $this->space;
    }

    /**
     * @param float $space
     */
    public function setSpace($space)
    {
        $this->space = $space;
    }

    /**
     * Set requirements
     *
     * @param array $requirements
     *
     * @return Blueprint
     */
    public function setRequirements($requirements)
    {
        $this->requirements = $requirements;

        return $this;
    }

    /**
     * Get requirements
     *
     * @return array
     */
    public function getRequirements()
    {
        return $this->requirements;
    }
}

