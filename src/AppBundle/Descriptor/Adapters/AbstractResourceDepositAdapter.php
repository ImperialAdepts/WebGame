<?php
namespace AppBundle\Descriptor\Adapters;

use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Entity; use PlanetBundle\Entity as PlanetEntity;
use PlanetBundle\Entity\ResourceDeposit;

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