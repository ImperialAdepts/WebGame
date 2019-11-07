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
     * @var Blueprint
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Resource\Blueprint")
     * @ORM\JoinColumn(name="main_blueprint_id", referencedColumnName="id", nullable=false)
     */
	private $mainProduct;

    /**
     * @var int
     * @ORM\Column(name="main_product_count", type="integer")
     */
	private $mainProductCount = 1;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="description", type="string", length=255)
	 */
	private $description = "";

	/**
     * Every resource will be consumed
	 * @var string[] blueprint_id => count
	 *
	 * @ORM\Column(name="inputs", type="json_array")
	 */
	private $inputs = [];

    /**
     * Every resource will be used
     * @var string[] concept => hours count
     *
     * @ORM\Column(name="tools", type="json_array")
     */
    private $tools = [];

    /**
     * Every resource will be produced
     * @var string[] blueprint_id => count
     *
     * @ORM\Column(name="products", type="json_array")
     */
    private $products = [];

    /**
     * BlueprintRecipe constructor.
     * @param Blueprint $mainProduct
     * @param integer|null $id
     */
    public function __construct(Blueprint $mainProduct, $id = null)
    {
        $this->id = $id;
        $this->mainProduct = $mainProduct;
        $mainProduct->addRecipe($this);
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
     * @return Blueprint
     */
    public function getMainProduct()
    {
        return $this->mainProduct;
    }

    /**
     * @param Blueprint $mainProduct
     */
    public function setMainProduct(Blueprint $mainProduct)
    {
        $this->mainProduct = $mainProduct;
        $mainProduct->addRecipe($this);
    }

    /**
     * @return int
     */
    public function getMainProductCount()
    {
        return $this->mainProductCount;
    }

    /**
     * @param int $mainProductCount
     */
    public function setMainProductCount($mainProductCount)
    {
        $this->mainProductCount = $mainProductCount;
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

    /**
     * @param $concept
     * @param int $workhours
     * @param int $count how many entities must be involved same time
     */
    public function addTool($concept, $workhours, $count = 1) {
        $this->tools[] = [
            'concept' => $concept,
            'workhours' => $workhours,
            'count' => $count,
        ];
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

