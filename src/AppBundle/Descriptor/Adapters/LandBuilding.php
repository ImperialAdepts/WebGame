<?php
namespace AppBundle\Descriptor\Adapters;

use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Descriptor\UseCaseTraitEnum;
use AppBundle\Entity;

class LandBuilding extends AbstractResourceDepositAdapter
{
    /**
     * @param ResourcefullInterface $resourcefull
     * @return LandBuilding[]
     */
    public static function in(ResourcefullInterface $resourcefull) {
        return parent::extractAdapterOfUseCase($resourcefull, UseCaseEnum::LAND_BUILDING);
    }

    public function getUsedArea() {
        return $this->getDeposit()->getAmount()*$this->getBlueprint()->getTraitValue(UseCaseTraitEnum::AREA_USED, 0);
    }

    /**
     * @param LandBuilding[] $buildings
     * @return int
     */
    public static function countUsedArea(array $buildings) {
        $area = 0;
        /** @var LandBuilding $building */
        foreach ($buildings as $building) {
            if ($building instanceof LandBuilding) {
                $area += $building->getUsedArea();
            }
        }
        return $area;
    }

    public function getWeight() {
        return $this->getDeposit()->getAmount()*$this->getBlueprint()->getTraitValue(UseCaseTraitEnum::WEIGHT, 0);
    }
}