<?php

namespace PlanetBundle\Entity;

use AppBundle\Descriptor\ResourcefullInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * RegionResourceDeposit
 *
 * @ORM\Table(name="region_resource_deposits")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RegionResourceDepositRepository")
 */
class RegionDeposit extends Deposit
{
    use RegionDependencyTrait;


    /**
     * @return ResourcefullInterface
     */
    public function getResourceHandler()
    {
        return $this->getRegion();
    }
}

