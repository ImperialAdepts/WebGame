<?php

namespace PlanetBundle\Entity;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Entity\Blueprint;
use AppBundle\Entity\ProjectNotification;
use Doctrine\ORM\Mapping as ORM;

/**
 * BuildingProject
 *
 * @ ORM\Entity(repositoryClass="PlanetBundle\Repository\BuildingProjectRepository")
 * @ORM\MappedSuperclass
 */
abstract class BuildingProject
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
	 * @ORM\OneToOne(targetEntity="PlanetBundle\Entity\Region", inversedBy="project")
     * @ORM\JoinColumns(
	 *  @ORM\JoinColumn(name="region_peak_center_id", referencedColumnName="peak_center_id", nullable=false),
	 *  @ORM\JoinColumn(name="region_peak_left_id", referencedColumnName="peak_left_id", nullable=false),
	 *  @ORM\JoinColumn(name="region_peak_right_id", referencedColumnName="peak_right_id", nullable=false)
     * )
	 */
	private $region;

	/**
	 * @var Blueprint
	 *
	 * @ ORM\ManyToOne(targetEntity="AppBundle\Entity\Blueprint")
	 * @ ORM\JoinColumn(name="building_blueprint_id", referencedColumnName="id")
	 */
	private $buildingBlueprint;

	/**
	 * @var \PlanetBundle\Entity\Human
	 *
	 * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Human")
	 * @ORM\JoinColumn(name="supervisor_id", referencedColumnName="id")
	 */
	private $supervisor;


	/**
	 * @return bool
	 */
	public abstract function isDone();

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
	 * @return \PlanetBundle\Entity\Human
	 */
	public function getSupervisor()
	{
		return $this->supervisor;
	}

	/**
	 * @param \PlanetBundle\Entity\Human $supervisor
	 */
	public function setSupervisor($supervisor)
	{
		$this->supervisor = $supervisor;
	}

	/**
	 * @return ProjectNotification[]
	 */
	public abstract function getNotifications();

}

