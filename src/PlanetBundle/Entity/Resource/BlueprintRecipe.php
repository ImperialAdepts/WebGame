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
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Resource\Thing", cascade={"persist"})
     * @ORM\JoinColumn(name="main_product_id", referencedColumnName="id", nullable=true)
     */
	private $mainProduct;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="description", type="string", length=255, unique=false)
	 */
	private $description = "";

    /**
     * @var BlueprintRecipeDeposit
     *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Resource\BlueprintRecipeDeposit", cascade={"persist"})
     * @ORM\JoinColumn(name="input_deposit_id", referencedColumnName="id", nullable=true)
     */
	private $inputs;

    /**
     * @var BlueprintRecipeDeposit
     *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Resource\BlueprintRecipeDeposit", cascade={"persist"})
     * @ORM\JoinColumn(name="tool_deposit_id", referencedColumnName="id", nullable=true)
     */
    private $tools;

    /**
     * @var BlueprintRecipeDeposit
     *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Resource\BlueprintRecipeDeposit", cascade={"persist"})
     * @ORM\JoinColumn(name="product_deposit_id", referencedColumnName="id", nullable=false)
     */
    private $products;

    /**
     * BlueprintRecipe constructor.
     * @param Thing $mainProduct
     * @param integer|null $id
     */
    public function __construct(Thing $mainProduct, $id = null)
    {
        $this->id = $id;
        $this->mainProduct = $mainProduct;
        $this->products = new BlueprintRecipeDeposit([$mainProduct]);
        $mainProduct->getBlueprint()->addRecipe($this);
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
	 * @param BlueprintRecipeDeposit $inputs
	 *
	 * @return BlueprintRecipe
	 */
	public function setInputs(BlueprintRecipeDeposit $inputs)
	{
		$this->inputs = $inputs;

		return $this;
	}

	/**
	 * Get requirements
	 *
	 * @return BlueprintRecipeDeposit
	 */
	public function getInputs()
	{
		return $this->inputs;
	}

    /**
     * @return BlueprintRecipeDeposit
     */
    public function getTools()
    {
        return $this->tools;
    }

    /**
     * @param BlueprintRecipeDeposit $tools
     */
    public function setTools(BlueprintRecipeDeposit $tools)
    {
        $this->tools = $tools;
    }

    /**
     * @return BlueprintRecipeDeposit
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param BlueprintRecipeDeposit $products
     */
    public function setProducts(BlueprintRecipeDeposit $products)
    {
        $this->products = $products;
    }

}

