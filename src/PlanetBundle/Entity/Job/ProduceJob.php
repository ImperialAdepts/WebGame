<?php

namespace PlanetBundle\Entity\Job;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Entity\Notification\ProjectNotification;
use PlanetBundle\Entity\Resource\Blueprint;
use PlanetBundle\Entity\Region;
use AppBundle\Entity\ResourceDeposit;
use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Entity\Resource\WorkSheetDependencyTrait;

/**
 * @ORM\Table(name="job_produces")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\JobRepository")
 */
class ProduceJob extends Job
{
    use WorkSheetDependencyTrait;
}

