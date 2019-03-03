<?php
namespace AppBundle\Descriptor\Adapters;

use AppBundle\Entity\ResourceDeposit;

class EnergySource extends AbstractResourceDepositAdapter
{
    public function getMinimalEnergyOutput() {
        return $this->getDeposit()->getAmount()*0;
    }

    public function getMaximalEnergyOutput() {
        return $this->getDeposit()->getAmount()*1000;
    }
}