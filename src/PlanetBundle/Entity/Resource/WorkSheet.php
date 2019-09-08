<?php

namespace PlanetBundle\Entity\Resource;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * class WorkSheet
 *
 * @ORM\Table(name="worksheets")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\WorkSheetRepository")
 */
class WorkSheet
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
	 * @var string[] resource_descriptor => count
	 *
	 * @ORM\Column(name="inputs", type="json_array")
	 */
	private $inputs;

    /**
     * Every resource will be used
     * @var string[] resource_descriptor => count
     *
     * @ORM\Column(name="tools", type="json_array")
     */
    private $tools;

    /**
     * Every resource will be produced
     * @var string[] resource_descriptor => count
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
	 * @return WorkSheet
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
	 * Get mandays
	 *
	 * @return int
	 */
	public function getMandays()
	{
	    if (isset($this->inputs[ResourceDescriptorEnum::MANDAY])) {
            return $this->inputs[ResourceDescriptorEnum::MANDAY];
        } else {
	        return 0;
        }
	}

	/**
	 * Set requirements
	 *
	 * @param array $inputs
	 *
	 * @return WorkSheet
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
}

