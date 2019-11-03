<?php

namespace PlanetBundle\Entity\Job;

use PlanetBundle\Entity\Deposit;
use PlanetBundle\Entity\Region;
use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Entity\Resource\ResourceDescriptor;
use PlanetBundle\Entity\Resource\ResourceDescriptorDependencyTrait;
use PlanetBundle\Entity\Resource\ThingDependencyTrait;

/**
 * @ORM\Table(name="job_sells")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\JobRepository")
 */
class SellJob extends Job
{
    use ResourceDescriptorDependencyTrait;
}

