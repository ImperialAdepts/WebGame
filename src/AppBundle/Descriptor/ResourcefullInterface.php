<?php
/**
 * Created by PhpStorm.
 * User: troi
 * Date: 8.3.19
 * Time: 12:44
 */

namespace AppBundle\Descriptor;

use AppBundle\Entity\ResourceDeposit;
use PlanetBundle\Entity\Blueprint;

interface ResourcefullInterface
{
    /**
     * @return \AppBundle\Entity\ResourceDeposit[]
     */
    public function getResourceDeposits();

    /**
     * @param string $resourceDescriptor
     * @return int
     */
    public function getResourceDepositAmount($resourceDescriptor);

    /**
     * @param Blueprint $blueprint
     * @param int $count
     */
    public function addResourceDeposit(Blueprint $blueprint, $count = 1);
}