<?php

namespace PlanetBundle\Entity\Job;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use PlanetBundle\Entity\Blueprint;
use AppBundle\Entity\Notification\ProjectNotification;
use PlanetBundle\Entity\Region;
use AppBundle\Entity\ResourceDeposit;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="region_buy_jobs")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\JobRepository")
 */
class BuyJob extends Job
{
    /**
     * @var ResourceDeposit
     *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\ResourceDeposit")
     * @ORM\JoinColumn(name="resource_deposit_id", referencedColumnName="id", nullable=true)
     */
    private $resourceDeposit;

    /**
     * @return ResourceDeposit
     */
    public function getResourceDeposit()
    {
        return $this->resourceDeposit;
    }

    /**
     * @param ResourceDeposit $resourceDeposit
     */
    public function setResourceDeposit(ResourceDeposit $resourceDeposit)
    {
        $this->resourceDeposit = $resourceDeposit;
    }
}

