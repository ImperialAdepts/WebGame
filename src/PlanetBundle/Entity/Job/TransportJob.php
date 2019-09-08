<?php

namespace PlanetBundle\Entity\Job;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Entity\Notification\ProjectNotification;
use PlanetBundle\Entity\DepositDependencyTrait;
use PlanetBundle\Entity\Region;
use AppBundle\Entity\ResourceDeposit;
use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Entity\Resource\BlueprintDependencyTrait;
use PlanetBundle\Entity\Resource\ResourceDescriptorDependencyTrait;
use PlanetBundle\Entity\Resource\ThingDependencyTrait;

/**
 * @ORM\Table(name="job_transports")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\JobRepository")
 */
class TransportJob extends Job
{
    // what move
    use BlueprintDependencyTrait;
    // to where
    use DepositDependencyTrait;
}

