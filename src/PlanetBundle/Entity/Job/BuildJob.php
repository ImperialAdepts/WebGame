<?php

namespace PlanetBundle\Entity\Job;

use PlanetBundle\Entity\Resource\Blueprint;
use AppBundle\Entity\Notification\ProjectNotification;
use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Entity\Resource\WorkSheetDependencyTrait;

/**
 * @ORM\Table(name="job_builds")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\JobRepository")
 */
class BuildJob extends Job
{
    use WorkSheetDependencyTrait;
}

