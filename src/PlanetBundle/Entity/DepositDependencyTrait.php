<?php

namespace PlanetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Entity\Deposit;
use PlanetBundle\Entity\Resource\DepositInterface;

trait DepositDependencyTrait
{
    /**
     * @var Deposit
     *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Deposit", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="deposit_id", referencedColumnName="id", nullable=true)
     */
    private $deposit;

    /**
     * @return DepositInterface
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