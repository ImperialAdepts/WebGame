<?php

namespace PlanetBundle\Maintainer;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Entity\ResourceDeposit;
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
            $births[$people->getResourceDescriptor()] = 1;//round($people->getDeposit()->getAmount() * $people->getFertilityRate() / 20) + 1;
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
            $unusedHumans->setDescription(ResourceDescriptorEnum::PEOPLE);
            $unusedHumans->setDeposit($deposit);
            $unusedHumans->setAmount($birthCount);
            $deposit->addResourceDescriptors($unusedHumans);
        } else {
            $firstHuman = array_pop($unusedHumansAdapter);
            $firstHuman->setAmount($firstHuman->getAmount() + $birthCount);
        }
    }

}