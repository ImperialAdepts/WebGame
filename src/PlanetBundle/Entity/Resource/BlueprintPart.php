<?php

namespace PlanetBundle\Entity\Resource;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * BlueprintPart
 *
 * @ORM\Table(name="blueprint_parts")
 * @ORM\Entity()
 */
class BlueprintPart
{
    use BlueprintDependencyTrait;

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
     *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Resource\Blueprint")
     * @ORM\JoinColumn(name="parent_blueprint_id", referencedColumnName="id", nullable=false)
     */
    private $parentBlueprint;

    /**
     * @var string
     *
     * @ORM\Column(name="usage_place", type="string", length=255)
     */
    private $usagePlace;

    /**
     * @var boolean
     *
     * @ORM\Column(name="integral", type="boolean", nullable=false)
     */
    private $integral = false;

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
    public function getParentBlueprint()
    {
        return $this->parentBlueprint;
    }

    /**
     * @param Blueprint $parentBlueprint
     */
    public function setParentBlueprint($parentBlueprint)
    {
        $this->parentBlueprint = $parentBlueprint;
    }

    /**
     * @return string
     */
    public function getUsagePlace()
    {
        return $this->usagePlace;
    }

    /**
     * @param string $usagePlace
     */
    public function setUsagePlace($usagePlace)
    {
        $this->usagePlace = $usagePlace;
    }

    /**
     * @return bool
     */
    public function isIntegral()
    {
        return $this->integral;
    }

    /**
     * @param bool $integral
     */
    public function setIntegral($integral)
    {
        $this->integral = $integral;
    }

}

