<?php
namespace AppBundle\Descriptor\Adapters;

use AppBundle\Entity\ResourceDeposit;

class LandBuilding extends AbstractResourceDepositAdapter
{
    public function getUsedArea() {
        return $this->getDeposit()->getAmount()*$this->getDeposit()->getSpace();
    }

    public function getWeight() {
        return $this->getDeposit()->getAmount()*$this->getDeposit()->getWeight();
    }
}