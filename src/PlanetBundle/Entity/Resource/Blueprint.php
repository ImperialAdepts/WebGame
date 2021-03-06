<?php

namespace PlanetBundle\Entity\Resource;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Concept\Concept;
use Tracy\Debugger;

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
	 * @ORM\Column(name="description", type="string", unique=false, length=255)
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
     * @var BlueprintPart[]
     *
     * @ORM\OneToMany(targetEntity="PlanetBundle\Entity\Resource\BlueprintPart", mappedBy="parentBlueprint", cascade={"all"}, orphanRemoval=true)
     */
    private $parts;

    /**
     * @var BlueprintRecipe[]
     *
     * @ORM\OneToMany(targetEntity="PlanetBundle\Entity\Resource\BlueprintRecipe", mappedBy="mainProduct", cascade={"all"}, orphanRemoval=true)
     */
    private $recipes;

    public function __construct()
    {
        $this->parts = new ArrayCollection();
        $this->recipes = new ArrayCollection();
    }

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
        foreach (class_uses($this->getConcept()) as $use) {
            yield $use;
        }
        foreach (class_parents($this->getConcept()) as $parent) {
            foreach (class_uses($parent) as $use) {
                yield $use;
            }
        }
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

    /**
     * @return BlueprintPart[]
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * @param BlueprintPart[] $parts
     */
    public function setParts($parts)
    {
        $this->parts = $parts;
    }

    /**
     * @return BlueprintRecipe[]
     */
    public function getRecipes()
    {
        return $this->recipes;
    }

    public function addRecipe(BlueprintRecipe $recipe) {
        $this->recipes->add($recipe);
    }

    /**
     * @param BlueprintRecipe[] $recipes
     */
    public function setRecipes($recipes)
    {
        $this->recipes = $recipes;
    }


    public function getPartsByUsage()
    {
        $parts = [];
        foreach ($this->getParts() as $blueprintPart) {
            $parts[$blueprintPart->getUsagePlace()] = $blueprintPart->getBlueprint();
        }
        return $parts;
    }

    /**
     * @param $partName
     * @return Blueprint
     */
    public function getPartBlueprint($partName)
    {
        $parts = $this->getPartsByUsage();
        if (isset($parts[$partName])) {
            return $parts[$partName];
        }
        return null;
    }

    public function setPartsByUsage($parts)
    {
        foreach ($parts as $usagePlace => $blueprint) {
            $this->addPart($usagePlace, $blueprint);
        }
    }

    public function addPart($usagePlace, Blueprint $blueprint) {
        $part = new BlueprintPart();
        $part->setParentBlueprint($this);
        $part->setBlueprint($blueprint);
        $part->setUsagePlace($usagePlace);
        $this->getParts()->add($part);
    }

	function __toString()
	{
		return $this->getResourceDescriptor().' '.$this->getDescription();
	}

    public function getConceptAdapter($currentData = []) {
        $conceptName = $this->getConcept();
        /** @var Concept $adapter */
        $adapter = new $conceptName();
        $adapter->setBlueprintSettings($this);
        $adapter->injectChangeableData($currentData);
        return $adapter;
    }

}

