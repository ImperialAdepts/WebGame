<?php

namespace PlanetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Entity\Deposit;

trait DepositDependencyTrait
{
    /**
     * @var Deposit
     *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Deposit")
     * @ORM\JoinColumn(name="deposit_id", referencedColumnName="id", nullable=true)
     */
    private $deposit;

    /**
     * @return Deposit
     */
    public function getDeposit()
    {
        return $this->deposit;
    }

    /**
     * @param Deposit $deposit
     */
    public function setDeposit(Deposit $deposit)
    {
        $this->deposit = $deposit;
    }
}