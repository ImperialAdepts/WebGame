<?php

namespace PlanetBundle\Entity\Resource;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * Blueprint
 *
 * @ORM\Table(name="blueprints")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\BlueprintRepository")
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
     * @var string[]
     *
     * @ORM\Column(name="use_cases", type="json_array")
     */
    private $useCases;

    /**
     * @var int|float[] traitName => value
     *
     * @ORM\Column(name="trait_values", type="json_array")
     */
    private $traitValues = [];

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
	 * @param string $resourceDescriptor
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
	 * @return string
	 */
	public function getResourceDescriptor()
	{
		return $this->resourceDescriptor;
	}

    /**
     * @return string[]
     */
    public function getUseCases()
    {
        return $this->useCases;
    }

    /**
     * @param $traitName
     * @param null $defaultValue
     * @return float|null
     */
    public function getTraitValue($traitName, $defaultValue = null)
    {
        $values = $this->getTraitValues();
        if (!isset($values[$traitName])) {
            return $defaultValue;
        }
        return $values[$traitName];
    }

    /**
     * @return int[]|float[]
     */
    public function getTraitValues()
    {
        return $this->traitValues;
    }

    /**
     * @param int[]|float[] $traitValues
     */
    public function setTraitValues(array $traitValues)
    {
        $this->traitValues = $traitValues;
    }

    /**
     * @param string[] $useCases
     */
    public function setUseCases(array $useCases)
    {
        $this->useCases = $useCases;
    }

	function __toString()
	{
		return $this->getResourceDescriptor().' '.$this->getDescription();
	}


}

