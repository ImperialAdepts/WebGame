<?php

namespace PlanetBundle\Entity\Resource;

use Doctrine\ORM\Mapping as ORM;

/**
 * class WorkSheet
 *
 * @ORM\Table(name="bluprint_recipes")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\BlueprintRecipeRepository")
 */
class BlueprintRecipe
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
     * Every resource will be consumed
	 * @var string[] blueprint_id => count
	 *
	 * @ORM\Column(name="inputs", type="json_array")
	 */
	private $inputs;

    /**
     * Every resource will be used
     * @var string[] concept => hours count
     *
     * @ORM\Column(name="tools", type="json_array")
     */
    private $tools;

    /**
     * Every resource will be produced
     * @var string[] blueprint_id => count
     *
     * @ORM\Column(name="products", type="json_array")
     */
    private $products;

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
	 * @return BlueprintRecipe
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
	 * Set requirements
	 *
	 * @param array $inputs
	 *
	 * @return BlueprintRecipe
	 */
	public function setInputs($inputs)
	{
		$this->inputs = $inputs;

		return $this;
	}

	/**
	 * Get requirements
	 *
	 * @return array
	 */
	public function getInputs()
	{
		return $this->inputs;
	}

    /**
     * @param Blueprint $blueprint
     * @param int $count
     */
    public function addInputBlueprint(Blueprint $blueprint, $count = 1) {
        $this->inputs['things'][$blueprint->getId()] = $count;
    }

    /**
     * @param Resource $resource
     * @param int $count
     */
    public function addInputResource($resource, $count = 1) {
        $this->inputs['resources'][$resource->getId()] = $count;
    }

    /**
     * @return string[]
     */
    public function getTools()
    {
        return $this->tools;
    }

    /**
     * @param string[] $tools
     */
    public function setTools($tools)
    {
        $this->tools = $tools;
    }

    public function addTool($concept, $count = 1) {
        $this->tools[$concept] = $count;
    }

    /**
     * @return string[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param string[] $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }

    public function addProductThing(Blueprint $blueprint, $count = 1) {
        $this->products['things'][$blueprint->getId()] = $count;
    }

    /**
     * @param Resource $resource
     * @param int $count
     */
    public function addProductResource($resource, $count = 1) {
        $this->products['resources'][$resource->getId()] = $count;
    }
}

