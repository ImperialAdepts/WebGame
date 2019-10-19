<?php

namespace PlanetBundle\Entity\Resource;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Concept\Concept;

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
     * @var int|float[] traitName => value
     *
     * @ORM\Column(name="trait_values", type="json_array")
     */
    private $traitValues = [];

    /**
     * @var string
     *
     * @ORM\Column(name="concept", type="string", length=255, nullable=true)
     */
    private $concept;

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
	 * Get resource
	 *
	 * @return string
	 */
	public function getResourceDescriptor()
	{
		return $this->getId();
	}

    /**
     * @return string[]
     */
    public function getUseCases()
    {
        return [];
        return $this->getConcept()->getUseCases();
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
     * @return string
     */
    public function getConcept()
    {
        return $this->concept;
    }

    /**
     * @param string $concept
     */
    public function setConcept($concept)
    {
        $this->concept = $concept;
    }

	function __toString()
	{
		return $this->getResourceDescriptor().' '.$this->getDescription();
	}

    public function addWorkSheet(WorkSheet $workSheet)
    {

    }

    public function getConceptAdapter($currentData) {
        $conceptName = 'PlanetBundle\\Concept\\' . $this->getConcept();
        /** @var Concept $adapter */
        $adapter = new $conceptName();
        $adapter->injectChangeableData($currentData);
        return $adapter;
    }

}

