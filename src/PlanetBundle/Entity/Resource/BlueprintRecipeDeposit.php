<?php

namespace PlanetBundle\Entity\Resource;

use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Entity\Deposit;

/**
 * @ORM\Table(name="deposit_recipes")
 * @ORM\Entity()
 */
class BlueprintRecipeDeposit extends Deposit
{

    public function getResourceHandler()
    {
        return null;
    }
}

