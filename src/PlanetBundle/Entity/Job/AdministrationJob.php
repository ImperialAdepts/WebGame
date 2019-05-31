<?php

namespace PlanetBundle\Entity\Job;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Entity\Notification\ProjectNotification;
use PlanetBundle\Entity\Region;
use AppBundle\Entity\ResourceDeposit;
use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Entity\SettlementDependencyTrait;

/**
 * @ORM\Table(name="region_administration_jobs")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\JobRepository")
 */
class AdministrationJob extends Job
{
}

