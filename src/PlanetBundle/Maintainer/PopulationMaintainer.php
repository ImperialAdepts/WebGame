<?php

namespace PlanetBundle\Maintainer;

use AppBundle\Descriptor\Adapters\BasicFood;
use AppBundle\Descriptor\Adapters\People;
use AppBundle\Descriptor\Adapters\Team;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\ResourcefullInterface;
use PlanetBundle\Entity\Peak;
use PlanetBundle\Entity\PeakDeposit;
use PlanetBundle\Entity\Region;
use AppBundle\Entity\ResourceDeposit;
use Doctrine\ORM\EntityManager;
use PlanetBundle\Entity\RegionDeposit;

class PopulationMaintainer
{
    public function getBirths(ResourcefullInterface $resourceHandler) {
        $peoples = People::in($resourceHandler);
        $births = [];
        foreach ($peoples as $people) {
            $births[$people->getResourceDescriptor()] = round($people->getDeposit()->getAmount() * $people->getFertilityRate() / 20) + 1;
        }
        return $births;
    }

    public function doBirths(ResourcefullInterface $resourceHandler) {
        $births = $this->getBirths($resourceHandler);
        $birthCount = 0;
        foreach ($births as $birth) {
            $birthCount += $birth;
        }

        $unusedHumansAdapter = People::findByDescriptor($resourceHandler, ResourceDescriptorEnum::PEOPLE);
        if ($unusedHumansAdapter == null) {
            if ($resourceHandler instanceof Region) {
                $unusedHumansDeposit = new RegionDeposit();
                $unusedHumansDeposit->setResourceDescriptor(ResourceDescriptorEnum::PEOPLE);
                $unusedHumansDeposit->setRegion($resourceHandler);
            }
            if ($resourceHandler instanceof Peak) {
                $unusedHumansDeposit = new PeakDeposit();
                $unusedHumansDeposit->setResourceDescriptor(ResourceDescriptorEnum::PEOPLE);
                $unusedHumansDeposit->setPeak($resourceHandler);
            }
            $unusedHumansDeposit->setAmount($birthCount);
        } else {
            $unusedHumansAdapter->getDeposit()->setAmount($unusedHumansAdapter->getDeposit()->getAmount() + $birthCount);
        }
    }

}