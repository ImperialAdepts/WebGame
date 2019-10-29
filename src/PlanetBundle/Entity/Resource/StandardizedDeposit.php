<?php

namespace PlanetBundle\Entity\Resource;

use AppBundle\Descriptor\Adapters\AbstractResourceDepositAdapter;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Descriptor\UseCaseTraitEnum;
use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Entity\Deposit;

/**
 * @ORM\Table(name="standardized_deposits")
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

