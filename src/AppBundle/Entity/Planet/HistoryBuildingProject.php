<?php

namespace AppBundle\Entity\Planet;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Entity\Blueprint;
use AppBundle\Entity\ProjectNotification;
use Doctrine\ORM\Mapping as ORM;

/**
 * HistoryBuildingProject
 *
 * @ORM\Table(name="planet_history_project")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Planet\BuildingProjectRepository")
 */
class HistoryBuildingProject extends BuildingProject
{
    /**
     * @var ProjectNotification[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Notification\ProjectNotification", mappedBy="project")
     */
    protected $notifications;

    /**
     * @return bool
     */
    public function isDone()
    {
        return true;
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

