<?php
namespace AppBundle\Descriptor\Adapters;

use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Descriptor\UseCaseTraitEnum;
use AppBundle\Entity;

class Warehouse extends AbstractResourceDepositAdapter
{
    /**
     * @param ResourcefullInterface $resourcefull
     * @return Warehouse[]
     */
    public static function in(ResourcefullInterface $resourcefull) {
        return parent::extractAdapterOfUseCase($resourcefull, UseCaseEnum::WAREHOUSE);
    }

    public function getSpaceCapacity() {
        return $this->getDeposit()->getAmount()*$this->getBlueprint()->getTraitValue(UseCaseTraitEnum::CAPACITY_SPACE, 0);
    }

    /**
     * @param Warehouse[] $buildings
     * @return int
     */
    public static function countSpaceCapacity(array $buildings) {
        $area = 0;
        /** @var Warehouse $building */
        foreach ($buildings as $building) {
            if ($building instanceof Warehouse) {
                $area += $building->getSpaceCapacity();
            }
        }
        return $area;
    }

    public function getWeightCapacity() {
        return $this->getDeposit()->getAmount()*$this->getBlueprint()->getTraitValue(UseCaseTraitEnum::CAPACITY_WEIGHT, 0);
    }

    /**
     * @param Warehouse[] $buildings
     * @return int
     */
    public static function countWeightCapacity(array $buildings) {
        $weight = 0;
        /** @var Warehouse $building */
        foreach ($buildings as $building) {
            if ($building instanceof Warehouse) {
                $weight += $building->getWeightCapacity();
            }
        }
        return $weight;
    }
}