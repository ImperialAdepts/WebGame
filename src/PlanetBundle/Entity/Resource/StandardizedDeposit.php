<?php

namespace PlanetBundle\Entity\Resource;

use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Entity\Deposit;

/**
 * @ORM\Table(name="deposit_standardized")
 * @ORM\Entity()
 */
class StandardizedDeposit extends Deposit
{
    /**
     * @var string
     * @ORM\Column(name="code", type="string")
     */
    private $code;

    /**
     * StandardizedDeposit constructor.
     * @param string $code
     */
    public function __construct($code)
    {
        parent::__construct();
        $this->code = $code;
    }


    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

}

