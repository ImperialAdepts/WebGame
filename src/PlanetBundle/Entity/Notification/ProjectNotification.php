<?php

namespace PlanetBundle\Entity\Notification;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use PlanetBundle\Entity\BuildingProject;
use PlanetBundle\Entity\CurrentBuildingProject;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectNotification
 *
 * @ORM\Table(name="notifications_project")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\NotificationRepository")
 */
class ProjectNotification extends GenericNotification
{
    /**
     * @var CurrentBuildingProject
     *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\CurrentBuildingProject")
     * @ORM\JoinColumn(fieldName="project_id", referencedColumnName="id", nullable=false)
     */
	private $project;

    /**
     * @return BuildingProject
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param BuildingProject $project
     */
    public function setProject(BuildingProject $project)
    {
        $this->project = $project;
    }


}

