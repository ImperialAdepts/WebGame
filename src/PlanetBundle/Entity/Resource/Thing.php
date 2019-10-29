<?php

namespace PlanetBundle\Entity\Resource;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Concept\Concept;

/**
 * Thing
 *
 * @ORM\Entity()
 * @ORM\Table(name="resource_descriptor_things")
 */
class Thing extends ResourceDescriptor
{
    use BlueprintDependencyTrait;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="description", type="string", nullable=true, length=255)
	 */
	private $description;

    /**
     * @var string[]
     *
     * @ORM\Column(name="use_cases", type="json_array")
     */
    private $useCases = [];

    /**
     * @var int|float[] traitName => value
     *
     * @ORM\Column(name="trait_values", type="json_array")
     */
    private $traitValues = [];

	/**
	 * Set name
	 *
	 * @param string $description
	 *
	 * @return Thing
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

    /**
     * @param string $useCase
     * @return boolean
     */
    public function hasUsecase($useCase) {
        return in_array($useCase, class_uses($this->getBlueprint()->getConcept()));
    }

    public function getConceptAdapter() {
        return $this->getBlueprint()->getConceptAdapter($this->getTraitValues());
    }
}

