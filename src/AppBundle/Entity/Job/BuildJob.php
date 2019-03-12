<?php

namespace AppBundle\Entity\Job;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Entity\Blueprint;
use AppBundle\Entity\Notification\ProjectNotification;
use AppBundle\Entity\Planet\Region;
use AppBundle\Entity\ResourceDeposit;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="planet_region_build_jobs")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JobRepository")
 */
class BuildJob extends Job
{
    /**
     * @var Blueprint
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Blueprint")
     * @ORM\JoinColumn(name="blueprint_id", referencedColumnName="id", nullable=true)
     */
    private $blueprint;

    /**
     * @return Blueprint
     */
    public function getBlueprint()
    {
        return $this->blueprint;
    }

    /**
     * @param Blueprint $blueprint
     */
    public function setBlueprint(Blueprint $blueprint)
    {
        $this->blueprint = $blueprint;
    }

}

