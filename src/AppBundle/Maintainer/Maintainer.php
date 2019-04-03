<?php

namespace AppBundle\Maintainer;

use AppBundle\Descriptor\Adapters\BasicFood;
use AppBundle\Descriptor\Adapters\Team;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Entity\Planet\Region;
use AppBundle\Entity\ResourceDeposit;
use Doctrine\ORM\EntityManager;

class Maintainer
{
    /** @var EntityManager */
    private $entityManager;
    /** @var FoodMaintainer */
    private $foodMaitainer;
    /** @var PopulationMaintainer */
    private $peopleMaintainer;

    /** @var string[] resource_descriptor => amount */
    private $resourceProductionCache = [];
    /** @var string[] resource_descriptor => amount */
    private $resourceConsumptionCache = [];

    /**
     * Maintainer constructor.
     * @param EntityManager $entityManager
     * @param FoodMaintainer $foodMaitainer
     * @param PopulationMaintainer $peopleMaintainer
     */
    public function __construct(EntityManager $entityManager, FoodMaintainer $foodMaitainer, PopulationMaintainer $peopleMaintainer)
    {
        $this->entityManager = $entityManager;
        $this->foodMaitainer = $foodMaitainer;
        $this->peopleMaintainer = $peopleMaintainer;
    }


    public function clearEmptyDeposits() {
        $emptyDeposits = $this->entityManager->getRepository(ResourceDeposit::class)->getEmpty();
        foreach ($emptyDeposits as $deposit) {
            $this->entityManager->remove($deposit);
        }
        $this->entityManager->flush();
    }

    /**
     * @param ResourcefullInterface $resourcefull
     * @param $resourceDescriptor
     * @return int
     */
    public function getConsumption(ResourcefullInterface $resourcefull, $resourceDescriptor) {
        // TODO: add cache
        $consumptionCount = 0;
        $consumptionBatches = [
            $this->foodMaitainer->getFoodConsumptionEstimation($resourcefull),
            ];
        foreach ($consumptionBatches as $consumptions) {
            if (isset($consumptions[$resourceDescriptor])) {
                $consumptionCount += $consumptions[$resourceDescriptor];
            }
        }
        return $consumptionCount;
    }

    /**
     * @param ResourcefullInterface $resourcefull
     * @param $resourceDescriptor
     * @return int
     */
    public function getProduction(ResourcefullInterface $resourcefull, $resourceDescriptor) {
        // TODO: add cache
        $productionCount = 0;
        $productionBatches = [
            $this->peopleMaintainer->getBirths($resourcefull),
        ];
        foreach ($productionBatches as $productions) {
            if (isset($productions[$resourceDescriptor])) {
                $productionCount += $productions[$resourceDescriptor];
            }
        }
        return $productionCount;
    }
}