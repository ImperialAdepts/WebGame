<?php

namespace PlanetBundle\Maintainer;

use AppBundle\Descriptor\ResourcefullInterface;
use Doctrine\Common\Persistence\ObjectManager;
use PlanetBundle\Entity\Deposit;
use PlanetBundle\Entity\Region;
use AppBundle\Entity\ResourceDeposit;
use Doctrine\ORM\EntityManager;

class Maintainer
{
    /** @var ObjectManager */
    private $planetEntityManager;
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
     * @param ObjectManager $planetEntityManager
     * @param FoodMaintainer $foodMaitainer
     * @param PopulationMaintainer $peopleMaintainer
     */
    public function __construct(ObjectManager $planetEntityManager, FoodMaintainer $foodMaitainer, PopulationMaintainer $peopleMaintainer)
    {
        $this->planetEntityManager = $planetEntityManager;
        $this->foodMaitainer = $foodMaitainer;
        $this->peopleMaintainer = $peopleMaintainer;
    }


    public function clearEmptyDeposits() {
        $emptyDeposits = $this->planetEntityManager->getRepository(\PlanetBundle\Entity\Resource\ResourceDescriptor::class)->findBy(['amount' => 0]);
        foreach ($emptyDeposits as $deposit) {
            $this->planetEntityManager->remove($deposit);
        }
        $this->planetEntityManager->flush();
    }

    /**
     * @param Deposit $deposit
     * @param $resourceDescriptor
     * @return int
     */
    public function getConsumption(Deposit $deposit, $resourceDescriptor) {
        // TODO: add cache
        $consumptionCount = 0;
        $consumptionBatches = [
            $this->foodMaitainer->getFoodConsumptionEstimation($deposit),
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