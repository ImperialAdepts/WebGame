<?php

namespace PlanetBundle\Entity;

use AppBundle\Descriptor\Adapters\AbstractResourceDepositAdapter;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Descriptor\UseCaseTraitEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * PeakResourceDeposit
 *
 * @ORM\Table(name="peak_resource_deposits")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PeakResourceDepositRepository")
 */
class PeakDeposit extends Deposit
{
    use PeakDependencyTrait;

    /**
     * @return ResourcefullInterface
     */
    public function getResourceHandler()
    {
        return $this->getPeak();
    }
}

