<?php

namespace AppBundle\Entity\Job;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Entity\Blueprint;
use AppBundle\Entity\Notification\ProjectNotification;
use AppBundle\Entity\Planet\Region;
use AppBundle\Entity\ResourceDeposit;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="planet_region_sell_jobs")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JobRepository")
 */
class SellJob extends Job
{
    /**
     * @var ResourceDeposit
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ResourceDeposit")
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

