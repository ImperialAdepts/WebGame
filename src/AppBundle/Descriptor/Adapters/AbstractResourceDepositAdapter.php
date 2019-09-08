<?php
namespace AppBundle\Descriptor\Adapters;

use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Entity; use PlanetBundle\Entity as PlanetEntity;
use PlanetBundle\Entity\Deposit;

class AbstractResourceDepositAdapter
{
    /** @var Deposit */
    private $deposit;

    /**
     * LivingBuilding constructor.
     * @param Deposit $deposit
     */
    public function __construct(Deposit $deposit)
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
        /** @var Deposit[] $deposits */
        foreach ($resourcefull->getDeposit() as $deposit) {
            /** @var Deposit $deposit */
            $useCaseAdapter = $deposit->asUseCase($useCaseName);
            if ($useCaseAdapter != null) {
                $adapters[] = $useCaseAdapter;
            }
        }
        return $adapters;
    }

    /**
     * @return  Deposit
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
     * @return PlanetEntity\Blueprint
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