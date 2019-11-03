<?php

namespace PlanetBundle\Maintainer;

use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\PlanetConnection\DynamicPlanetConnector;
use PlanetBundle\Concept;
use PlanetBundle\Entity\Deposit;
use PlanetBundle\Entity\Region;
use Doctrine\ORM\EntityManager;
use PlanetBundle\Entity\Resource\DepositInterface;
use PlanetBundle\Entity\Resource\Thing;

class FoodMaintainer
{
    const PEOPLE_STARVATION_RATIO = 0.3;

    /**
     * @param Deposit $deposit
     * @return int[] resource_descriptor => unit amount consumption
     */
    public function getFoodConsumptionEstimation(DepositInterface $deposit) {
        $foodEnergyNeeded = 0;
        /** @var Thing $people */
        foreach ($deposit->filterByConcept(Concept\People::class) as $people) {
            $foodEnergyNeeded += $people->getConceptAdapter()->getBasalMetabolism(DynamicPlanetConnector::getPlanet())*$people->getAmount();
        }
        $foods = $deposit->filterByConcept(Concept\Food::class);
        $allEnergy = 0;
        /** @var Thing $food */
        foreach ($foods as $food) {
            $allEnergy += $food->getConceptAdapter()->getEnergy()*$food->getAmount();
        }
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