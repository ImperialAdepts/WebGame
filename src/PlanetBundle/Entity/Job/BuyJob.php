<?php

namespace PlanetBundle\Entity\Job;

use PlanetBundle\Entity\Resource\Blueprint;
use AppBundle\Entity\Notification\ProjectNotification;
use PlanetBundle\Entity\Region;
use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Entity\DepositDependencyTrait;
use PlanetBundle\Entity\Resource\BlueprintDependencyTrait;

/**
 * @ORM\Table(name="job_buys")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\JobRepository")
 */
class BuyJob extends Job
{
    use BlueprintDependencyTrait;
}

