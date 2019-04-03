<?php
/**
 * Created by PhpStorm.
 * User: troi
 * Date: 3.3.19
 * Time: 17:11
 */

namespace AppBundle\Descriptor\Adapters;


use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Entity;
use AppBundle\Entity\ResourceDeposit;
use Tracy\Debugger;

class AbstractResourceDepositAdapter
{
    /** @var ResourceDeposit */
    private $deposit;

    /**
     * LivingBuilding constructor.
     * @param ResourceDeposit $deposit
     */
    public function __construct(ResourceDeposit $deposit)
    {
        $this->deposit = $deposit;
    }

    /**
     * @param ResourcefullInterface $resourcefull
     * @param string $useCaseName
     * @return AbstractResourceDepositAdapter[]
     */
    public static function extractAdapterOfUseCase(ResourcefullInterface $resourcefull, $useCaseName) {
        /** @var AbstractResourceDepositAdapter[] $adapters */
        $adapters = [];
        /** @var ResourceDeposit[] $deposits */
        foreach ($resourcefull->getResourceDeposits() as $deposit) {
            /** @var ResourceDeposit $deposit */
            $useCaseAdapter = $deposit->asUseCase($useCaseName);
            if ($useCaseAdapter != null) {
                $adapters[] = $useCaseAdapter;
            }
        }
        return $adapters;
    }

    /**
     * @return  ResourceDeposit
     */
    public function getDeposit() {
        return $this->deposit;
    }

    /**
     * @return int|float
     */
    public function getAmount()
    {
        return $this->deposit->getAmount();
    }

    /**
     * @return Entity\Blueprint
     */
    public function getBlueprint()
    {
        return $this->deposit->getBlueprint();
    }

    /**
     * @return string
     */
    public function getResourceDescriptor()
    {
        return $this->deposit->getResourceDescriptor();
    }
}