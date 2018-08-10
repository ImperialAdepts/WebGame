<?php

namespace AppBundle\Entity\Planet;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Entity\Blueprint;
use Doctrine\ORM\Mapping as ORM;

/**
 * BuildingProject
 *
 * @ORM\Table(name="planet_building_project")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Planet\BuildingProjectRepository")
 */
class BuildingProject
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
	 * @var Region
	 *
	 * @ORM\OneToOne(targetEntity="AppBundle\Entity\Planet\Region", inversedBy="project")
	 * @ORM\JoinColumn(name="region_uuid", referencedColumnName="uuid")
	 */
	private $region;

	/**
	 * @var Blueprint
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Blueprint")
	 * @ORM\JoinColumn(name="building_blueprint_id", referencedColumnName="id")
	 */
	private $buildingBlueprint;

	/**
	 * @var \AppBundle\Entity\Human
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Human")
	 * @ORM\JoinColumn(name="supervisor_id", referencedColumnName="id")
	 */
	private $supervisor;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="priority", type="integer")
	 */
	private $priority;

	/**
	 * @var float[] resourceDescriptor => amount
	 *
	 * @ORM\Column(name="missing_resources", type="array")
	 */
	private $missingResources;

	/**
	 * @var string[]
	 *
	 * @ORM\Column(name="steplogs", type="array")
	 */
	private $steplogs;

	/**
	 * @return bool
	 */
	public function isDone()
	{
		$resourceLeft = 0;
		foreach ($this->getMissingResources() as $resource => $amount) {
			$resourceLeft += $amount;
		}
		return $resourceLeft <= 0;
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
	 * Set region
	 *
	 * @param Region $region
	 *
	 * @return BuildingProject
	 */
	public function setRegion($region)
	{
		$this->region = $region;

		return $this;
	}

	/**
	 * Get region
	 *
	 * @return Region
	 */
	public function getRegion()
	{
		return $this->region;
	}

	/**
	 * Set building
	 *
	 * @param Blueprint $buildingBlueprint
	 *
	 * @return BuildingProject
	 */
	public function setBuildingBlueprint(Blueprint $buildingBlueprint)
	{
		$this->buildingBlueprint = $buildingBlueprint;

		return $this;
	}

	/**
	 * Get building
	 *
	 * @return Blueprint
	 */
	public function getBuildingBlueprint()
	{
		return $this->buildingBlueprint;
	}

	/**
	 * @return \AppBundle\Entity\Human
	 */
	public function getSupervisor()
	{
		return $this->supervisor;
	}

	/**
	 * @param \AppBundle\Entity\Human $supervisor
	 */
	public function setSupervisor($supervisor)
	{
		$this->supervisor = $supervisor;
	}

	/**
	 * Set priority
	 *
	 * @param integer $priority
	 *
	 * @return BuildingProject
	 */
	public function setPriority($priority)
	{
		$this->priority = $priority;

		return $this;
	}

	/**
	 * Get priority
	 *
	 * @return int
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * @return int
	 */
	public function getMandaysLeft()
	{
		if (!array_key_exists(ResourceDescriptorEnum::MANDAY, $this->missingResources)) {
			return 0;
		}
		return $this->missingResources[ResourceDescriptorEnum::MANDAY];
	}

	/**
	 * @param int $mandaysLeft
	 */
	public function setMandaysLeft($mandaysLeft)
	{
		$this->missingResources[ResourceDescriptorEnum::MANDAY] = $mandaysLeft;
	}

	/**
	 * @return float[]
	 */
	public function getMissingResources()
	{
		return $this->missingResources;
	}

	public function getMissingResource($resource)
	{
		if (!array_key_exists($resource, $this->missingResources)) {
			return 0;
		}
		return $this->missingResources[$resource];
	}

	/**
	 * @param float[] $missingResources
	 */
	public function setMissingResources($missingResources)
	{
		$this->missingResources = $missingResources;
	}

	public function setMissingResource($resource, $count)
	{
		$this->missingResources[$resource] = $count;
	}

	/**
	 * @return \string[]
	 */
	public function getSteplogs()
	{
		return $this->steplogs;
	}

	/**
	 * @param \string[] $steplogs
	 */
	public function setSteplogs($steplogs)
	{
		$this->steplogs = $steplogs;
	}

	/**
	 * @param \string[] $steplogs
	 */
	public function addSteplog($steplog)
	{
		$this->steplogs[] = $steplog;
	}

}

