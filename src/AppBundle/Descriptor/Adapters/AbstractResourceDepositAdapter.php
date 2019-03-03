<?php
/**
 * Created by PhpStorm.
 * User: troi
 * Date: 3.3.19
 * Time: 17:11
 */

namespace AppBundle\Descriptor\Adapters;


use AppBundle\Entity\ResourceDeposit;

class AbstractResourceDepositAdapter
{
    /** @var ResourceDeposit */
    private $deposit;

    /**
     * LivingBuilding constructor.
     * @param ResourceDeposit $deposit
     */
    public function __construct(ResourceDeposit $deposit)
    {
        $this->deposit = $deposit;
    }

    /**
     * @return  ResourceDeposit
     */
    public function getDeposit() {
        return $this->deposit;
    }

    public function getAmount()
    {
        return $this->deposit->getAmount();
    }

    public function getBlueprint()
    {
        return $this->deposit->getBlueprint();
    }

    public function getResourceDescriptor()
    {
        return $this->deposit->getResourceDescriptor();
    }
}