<?php

namespace PlanetBundle\Maintainer;

use AppBundle\Descriptor\Adapters\BasicFood;
use AppBundle\Descriptor\Adapters\People;
use AppBundle\Descriptor\Adapters\Team;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\ResourcefullInterface;
use PlanetBundle\Concept\Food;
use PlanetBundle\Entity\Deposit;
use PlanetBundle\Entity\Region;
use AppBundle\Entity\ResourceDeposit;
use Doctrine\ORM\EntityManager;
use PlanetBundle\Entity\Resource\DepositInterface;

class FoodMaintainer
{
    const PEOPLE_STARVATION_RATIO = 0.3;

    /**
     * @param Deposit $deposit
     * @return int[] resource_descriptor => unit amount consumption
     */
    public function getFoodConsumptionEstimation(DepositInterface $deposit) {
        $foodEnergyNeeded = People::countFoodEnergyConsumption($deposit->filterByConcept(People::class));
        $foods = $deposit->filterByConcept(Food::class);
        $allEnergy = BasicFood::countEnergy($foods);
        if ($allEnergy <= 0) {
            return [];
        }
        $consumptionPercentace = $foodEnergyNeeded / $allEnergy;
        $foodConsumption = [];
        foreach ($foods as $food) {
            $units = $food->getUnitsByEnergy(ceil($food->getEnergy()*$consumptionPercentace));
            $foodConsumption[$food->getResourceDescriptor()] = $units;
            $foodEnergyNeeded -= $food->getEnergy($units);
        }
        return $foodConsumption;
    }

    /**
     * @param Region $region
     * @return string[] resorce_descriptor => change amount
     */
    public function eatFood(Region $region) {
        $consumption = $this->getFoodConsumptionEstimation($region->getDeposit());
        $missingEnergy = 0;

        $realChanges = [];
        foreach ($consumption as $foodResourceDescriptor => $consumedAmount) {
            $food = BasicFood::findByDescriptor($region, $foodResourceDescriptor);
            if ($consumedAmount > $food->getAmount()) {
                $missingEnergy += $food->getEnergy($consumedAmount - $food->getAmount());
                $realConsumedFood = $food->getAmount();
            } else {
                $realConsumedFood = $consumedAmount;
            }
            $realChanges[$foodResourceDescriptor] = $realConsumedFood;
            $food->getDeposit()->setAmount($food->getAmount() - $realConsumedFood);
        }

        if ($missingEnergy > 0) {
            $hungryPeople = floor($missingEnergy / 3000);
            $diedPeople = round($hungryPeople * self::PEOPLE_STARVATION_RATIO) + 1;
            // TODO: Kill people
        }
        return $realChanges;
    }
}