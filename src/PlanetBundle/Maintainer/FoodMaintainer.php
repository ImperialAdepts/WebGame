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
use Tracy\Debugger;

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
            $foodConcept = $food->getConceptAdapter();
            $units = $this->countFoodUnits($foodConcept, $foodConcept->getEnergy()*$food->getAmount()*$consumptionPercentace);

            if (isset($foodConsumption[$food->getId()])) {
                $foodConsumption[$food->getId()] += $units;
            } else {
                $foodConsumption[$food->getId()] = $units;
            }
            $foodEnergyNeeded -= $foodConcept->getEnergy()*$units;

            if ($foodEnergyNeeded <= 0) break;
        }
        return $foodConsumption;
    }

    private function countFoodUnits(Concept\Food $food, $energy) {
        return ceil($energy / $food->getEnergy());
    }

    /**
     * @param Deposit $deposit
     * @return string[] resorce_descriptor => change amount
     */
    public function eatFood(Deposit $deposit) {
        $consumption = $this->getFoodConsumptionEstimation($deposit);
        $missingFood = 0;

        $realChanges = [];
        $foods = $deposit->filterByConcept(Concept\Food::class);
        foreach ($foods as $food) {
            if (isset($consumption[$food->getId()])) {
                $amountToConsume = $consumption[$food->getId()];
                if ($amountToConsume > $food->getAmount()) {
                    $missingFood[$food->getId()] = $amountToConsume - $food->getAmount();
                    $realConsumedFood = $food->getAmount();
                } else {
                    $realConsumedFood = $amountToConsume;
                }
                $food->setAmount($food->getAmount() - $realConsumedFood);
                $realChanges[$food->getId()] = $consumption[$food->getId()];
            }
        }

        if ($missingFood > 0) {
            $hungryPeople = floor($missingFood / 3000);
            $diedPeople = round($hungryPeople * self::PEOPLE_STARVATION_RATIO) + 1;
            // TODO: Kill people
        }
        return $realChanges;
    }
}