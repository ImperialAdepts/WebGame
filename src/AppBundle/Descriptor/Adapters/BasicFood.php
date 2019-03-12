<?php
namespace AppBundle\Descriptor\Adapters;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Descriptor\UseCaseTraitEnum;
use AppBundle\Entity;

class BasicFood extends AbstractResourceDepositAdapter
{
    /**
     * @param ResourcefullInterface $resourcefull
     * @return BasicFood[]
     */
    public static function in(ResourcefullInterface $resourcefull) {
        return parent::extractAdapterOfUseCase($resourcefull, UseCaseEnum::BASIC_FOOD);
    }


    /**
     * @param BasicFood[] $foods
     * @return int
     */
    public static function countEnergy(array $foods) {
        $energy = 0;
        /** @var BasicFood $food */
        foreach ($foods as $food) {
            if ($food instanceof BasicFood) {
                $energy += $food->getEnergy();
            }
        }
        return $energy;
    }

    /**
     * @return int
     */
    public function getEnergyPerUnit() {
        return $this->getBlueprint()->getTraitValue(UseCaseTraitEnum::FOOD_ENERGY, 0);
    }

    public function getEnergy() {
        return $this->getDeposit()->getAmount()*$this->getEnergyPerUnit();
    }
}