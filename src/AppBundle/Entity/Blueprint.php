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
     * Every resource will be consumed
	 * @var string[] resource_descriptor => count
	 *
	 * @ORM\Column(name="resource_requirements", type="json_array")
	 */
	private $resourceRequirements;

    /**
     * Every resource by usecase will be consumed
     * @var string[] usecase_name => [trait_name => value]
     *
     * @ORM\Column(name="use_case_requirements", type="json_array")
     */
    private $useCaseRequirements;

    /**
     * Must be at place
     * @var string[] resource_descriptor => count
     *
     * @ORM\Column(name="constraints", type="json_array")
     */
    private $constraints;

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
	 * Get mandays
	 *
	 * @return int
	 */
	public function getMandays()
	{
	    if (isset($this->resourceRequirements[ResourceDescriptorEnum::MANDAY])) {
            return $this->resourceRequirements[ResourceDescriptorEnum::MANDAY];
        } else {
	        return 0;
        }
	}

	/**
	 * Set requirements
	 *
	 * @param array $resourceRequirements
	 *
	 * @return Blueprint
	 */
	public function setResourceRequirements($resourceRequirements)
	{
		$this->resourceRequirements = $resourceRequirements;

		return $this;
	}

	/**
	 * Get requirements
	 *
	 * @return array
	 */
	public function getResourceRequirements()
	{
		return $this->resourceRequirements;
	}

    /**
     * @return string[]
     */
    public function getUseCaseRequirements()
    {
        return $this->useCaseRequirements;
    }

    /**
     * @param string[] $useCaseRequirements
     */
    public function setUseCaseRequirements($useCaseRequirements)
    {
        $this->useCaseRequirements = $useCaseRequirements;
    }

    /**
     * @return string[]
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * @param string[] $constraints
     */
    public function setConstraints($constraints)
    {
        $this->constraints = $constraints;
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
        if (!isset($this->traitValues[$traitName])) {
            return $defaultValue;
        }
        return $this->traitValues[$traitName];
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

	public function getResourcesPerManday()
	{
		$requirementsPerManday = [];
		$mandays = $this->resourceRequirements[ResourceDescriptorEnum::MANDAY];
		foreach ($this->resourceRequirements as $resource => $count) {
			$requirementsPerManday[$resource] = $count / $mandays;
		}
		return $requirementsPerManday;
	}

	function __toString()
	{
		return $this->getResourceDescriptor().' '.$this->getDescription();
	}


}

