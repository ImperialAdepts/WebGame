<?php
namespace AppBundle\Descriptor\Adapters;

use AppBundle\Entity\ResourceDeposit;

class LivingBuilding extends AbstractResourceDepositAdapter
{
    public function getLivingCapacity() {
        return $this->getDeposit()->getAmount()*10;
    }
}