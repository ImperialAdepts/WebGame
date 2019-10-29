<?php
namespace AppBundle\Descriptor\Adapters;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Descriptor\UseCaseTraitEnum;
use AppBundle\Entity; use PlanetBundle\Entity as PlanetEntity;

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
     * @param PlanetEntity\Region $region
     * @param string $resourceDescriptor
     * @return BasicFood
     */
    public static function findByDescriptor(PlanetEntity\Region $region, $resourceDescriptor) {
        $descriptor = $region->getResourceDeposit($resourceDescriptor);
        return $descriptor->asUseCase(UseCaseEnum::BASIC_FOOD);
    }


    /**
     * @param BasicFood[] $foods
     * @return int
     */
    public static function countEnergy($foods) {
        $energy = 0;
        /** @var BasicFood $food */
        foreach ($foods as $food) {
            if ($food instanceof BasicFood) {
                $energy += $food->getEnergy();
            }
        }
        return $energy;
    }

    public static function countVariety($foodChanges) {
        if (count($foodChanges) == 0) return 0;
        $average = 0;
        foreach ($foodChanges as $resourceDescriptor => $count) {
        }
        $average = $average / count($foodChanges);
        $varietyFactor = 0;
        foreach ($foodChanges as $resourceDescriptor => $count) {
            if ($count > $average) {
                $varietyFactor += 1;
            } else {
                $varietyFactor += ($average / $count);
            }
        }
        return $varietyFactor;
    }

    /**
     * @return int Joule
     */
    public function getEnergyPerUnit() {
        return $this->getBlueprint()->getTraitValue(UseCaseTraitEnum::FOOD_ENERGY, 0);
    }

    public function getEnergy($unitCount = null) {
        if ($unitCount != null) {
            return $unitCount*$this->getEnergyPerUnit();
        }
        return $this->getDeposit()->getAmount()*$this->getEnergyPerUnit();
    }

    public function getUnitsByEnergy($energy) {
        return ceil($energy / $this->getEnergyPerUnit());
    }

    public static function eatEnergy(array $foods, $energyAmount) {

    }
}