<?php

namespace PlanetBundle\Entity;

use AppBundle\Descriptor\Adapters\AbstractResourceDepositAdapter;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Descriptor\UseCaseTraitEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * RegionResourceDeposit
 *
 * @ORM\Table(name="region_resource_deposits")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RegionResourceDepositRepository")
 */
class RegionResourceDeposit extends ResourceDeposit
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

