<?php
/**
 * Created by PhpStorm.
 * User: troi
 * Date: 8.3.19
 * Time: 12:44
 */

namespace AppBundle\Descriptor;

interface ResourcefullInterface
{
    /**
     * @return \AppBundle\Entity\ResourceDeposit[]
     */
    public function getResourceDeposits();
}