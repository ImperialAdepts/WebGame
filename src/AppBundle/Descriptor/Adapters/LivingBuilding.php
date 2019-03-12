<?php
namespace AppBundle\Descriptor\Adapters;

use AppBundle\Descriptor\ResourceTraitEnum;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Descriptor\UseCaseTraitEnum;
use AppBundle\Entity;
use AppBundle\Entity\ResourceDeposit;

class LivingBuilding extends AbstractResourceDepositAdapter
{
    /**
     * @param Entity\Planet\Settlement $settlement
     * @return LivingBuilding[]
     */
    public static function in(Entity\Planet\Settlement $settlement) {
        return parent::extractAdapterOfUseCase($settlement, UseCaseEnum::LIVING_BUILDINGS);
    }

    /**
     * @return int
     */
    public function getLivingCapacity() {
        return $this->getDeposit()->getAmount()*$this->getLivingCapacityUnit();
    }

    /**
     * @return int
     */
    public function getLivingCapacityUnit() {
        return $this->getBlueprint()->getTraitValue(UseCaseTraitEnum::CAPACITY_HUMAN, 0);
    }

    /**
     * @param LivingBuilding[] $houses
     * @return int
     */
    public static function countLivingCapacity(array $houses) {
        $housingCapacity = 0;
        /** @var LivingBuilding $house */
        foreach ($houses as $house) {
            if ($house instanceof LivingBuilding) {
                $housingCapacity += $house->getLivingCapacity();
            }
        }
        return $housingCapacity;
    }
}