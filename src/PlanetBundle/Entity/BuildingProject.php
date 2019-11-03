<?php

namespace PlanetBundle\Entity;

use AppBundle\Entity\ProjectNotification;
use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Entity\Resource\BlueprintDependencyTrait;

/**
 * BuildingProject
 *
 * @ ORM\Entity(repositoryClass="PlanetBundle\Repository\BuildingProjectRepository")
 * @ORM\MappedSuperclass
 */
abstract class BuildingProject
{
    use DepositDependencyTrait;
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
	 * Set building
	 *
	 * @param Blueprint $buildingBlueprint
	 *
	 * @return BuildingProject
	 */
	public function setBuildingBlueprint(Blueprint $buildingBlueprint)
	{
		$this->setBlueprint($buildingBlueprint);

		return $this;
	}

	/**
	 * Get building
	 *
	 * @return Blueprint
	 */
	public function getBuildingBlueprint()
	{
		return $this->getBlueprint();
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

