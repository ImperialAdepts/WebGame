<?php

namespace PlanetBundle\Entity\Job;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Entity\Notification\ProjectNotification;
use PlanetBundle\Entity\Region;
use AppBundle\Entity\ResourceDeposit;
use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Entity\SettlementDependencyTrait;

/**
 * @ORM\Table(name="job_administrations")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\JobRepository")
 */
class AdministrationJob extends Job
{
    /**
     * @var int
     *
     * @ORM\Column(name="time_spent", type="integer")
     */
    private $timeSpent = 0;

    /**
     * @return int
     */
    public function getTimeSpent()
    {
        return $this->timeSpent;
    }

    /**
     * @param int $timeSpent
     */
    public function setTimeSpent($timeSpent)
    {
        $this->timeSpent = $timeSpent;
    }


}

