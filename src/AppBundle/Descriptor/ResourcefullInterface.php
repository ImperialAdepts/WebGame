<?php

namespace AppBundle\Descriptor;

use AppBundle\Entity\ResourceDeposit;
use PlanetBundle\Entity\Deposit;
use PlanetBundle\Entity\Resource\Blueprint;
use PlanetBundle\Entity\Resource\DepositInterface;

interface ResourcefullInterface
{
    /**
     * @return DepositInterface
     */
    public function getDeposit();
}