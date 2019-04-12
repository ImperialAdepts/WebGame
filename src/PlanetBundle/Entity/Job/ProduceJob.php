<?php

namespace PlanetBundle\Entity\Job;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Entity\Notification\ProjectNotification;
use PlanetBundle\Entity\Blueprint;
use PlanetBundle\Entity\Region;
use AppBundle\Entity\ResourceDeposit;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="region_produce_jobs")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\JobRepository")
 */
class ProduceJob extends Job
{
    /**
     * @var Blueprint
     *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Blueprint")
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

