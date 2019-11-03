<?php

namespace PlanetBundle\Entity;

use PlanetBundle\Entity\Resource\Blueprint;
use Doctrine\ORM\Mapping as ORM;

/**
 * CurrentBuildingProject
 *
 * @ORM\Table(name="building_project")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\BuildingProjectRepository")
 */
class CurrentBuildingProject extends BuildingProject
{
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
     * @var ProjectNotification[]
     *
     * @ORM\OneToMany(targetEntity="PlanetBundle\Entity\Notification\ProjectNotification", mappedBy="project", cascade={"persist"})
     */
    protected $notifications;

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
		return 666;
	}

	/**
	 * @param int $mandaysLeft
	 */
	public function setMandaysLeft($mandaysLeft)
	{
		$this->missingResources['ResourceDescriptorEnum::MANDAY'] = $mandaysLeft;
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
     * @param string $notificationText
     */
	public function addNotification($notificationText)
	{
        $projectNotification = new ProjectNotification();
        $projectNotification->setProject($this);
        $projectNotification->setDescription($notificationText);
        $projectNotification->setCreationTime(time());
        $notifications = $this->getNotifications();
		$notifications[] = $projectNotification;
		$this->setNotifications($notifications);
	}

    /**
     * @return ProjectNotification[]
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * @param ProjectNotification[] $notifications
     */
    public function setNotifications($notifications)
    {
        $this->notifications = $notifications;
    }

}

