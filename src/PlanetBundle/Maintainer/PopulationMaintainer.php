<?php

namespace PlanetBundle\Maintainer;

use AppBundle\PlanetConnection\DynamicPlanetConnector;
use PlanetBundle\Concept\People;
use PlanetBundle\Entity\Deposit;
use PlanetBundle\Entity\Resource\DepositInterface;
use PlanetBundle\Entity\Resource\Thing;

class PopulationMaintainer
{
    public function getBirths(DepositInterface $deposit) {
        /** @var Thing[] $peoples */
        $peoples = $deposit->filterByConcept(People::class);
        $births = [];
        foreach ($peoples as $people) {
            $births[$people->getBlueprint()->getDescription()] = round($people->getAmount() * $people->getConceptAdapter()->getFertilityRate(DynamicPlanetConnector::getPlanet()) / 20) + 1;
        }
        return $births;
    }

    public function doBirths(Deposit $deposit) {
        $births = $this->getBirths($deposit);
        $birthCount = 0;
        foreach ($births as $birth) {
            $birthCount += $birth;
        }

        /** @var Thing[] $unusedHumansAdapter */
        $unusedHumansAdapter = $deposit->filterByConcept(People::class);
        if (empty($unusedHumansAdapter)) {
            $unusedHumans = new Thing();
            $unusedHumans->setDescription(People::class);
            $unusedHumans->setDeposit($deposit);
            $unusedHumans->setAmount($birthCount);
            $deposit->addResourceDescriptors($unusedHumans);
        } else {
            $firstHuman = array_pop($unusedHumansAdapter);
            $firstHuman->setAmount($firstHuman->getAmount() + $birthCount);
        }
    }

}