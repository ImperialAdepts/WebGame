<?php
namespace PlanetBundle\UseCase;

use PlanetBundle\Annotation\Concept\Persistent;
use PlanetBundle\UseCase;

trait SpaceShipDeposit
{
    use UseCase\Deposit;

    /**
     * @var integer
     * @Persistent(type="PlanetBundle\Entity\Deposit")
     */
    private $depositId;

    /**
     * @return int
     */
    public function getDepositId()
    {
        return $this->depositId;
    }

    /**
     * @param int $depositId
     */
    public function setDepositId($depositId)
    {
        $this->depositId = $depositId;
    }

}