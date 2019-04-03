<?php

namespace AppBundle\Maintainer;

use AppBundle\Descriptor\Adapters\BasicFood;
use AppBundle\Descriptor\Adapters\People;
use AppBundle\Descriptor\Adapters\Team;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Entity\Planet\Region;
use AppBundle\Entity\ResourceDeposit;
use Doctrine\ORM\EntityManager;

class FoodMaintainer
{
    const PEOPLE_STARVATION_RATIO = 0.3;

    /** @var EntityManager */
    private $entityManager;

    /**
     * Maintainer constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param ResourcefullInterface $resourceHandler
     * @return int[] resource_descriptor => unit amount consumption
     */
    public function getFoodConsumptionEstimation(ResourcefullInterface $resourceHandler) {
        $foodEnergyNeeded = People::countFoodEnergyConsumption(People::in($resourceHandler));
        $foods = BasicFood::in($resourceHandler);
        $allEnergy = BasicFood::countEnergy($foods);
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
        $consumption = $this->getFoodConsumptionEstimation($region);
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