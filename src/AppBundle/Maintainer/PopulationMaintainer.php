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

class PopulationMaintainer
{
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

    public function getBirths(ResourcefullInterface $resourceHandler) {
        $peoples = People::in($resourceHandler);
        $births = [];
        foreach ($peoples as $people) {
            $births[$people->getResourceDescriptor()] = round($people->getDeposit()->getAmount() * $people->getFertilityRate() / 20) + 1;
        }
        return $births;
    }

    public function doBirths(Region $resourceHandler) {
        $births = $this->getBirths($resourceHandler);
        $unusedHumans = People::findByDescriptor($resourceHandler, ResourceDescriptorEnum::PEOPLE);
        foreach ($births as $birth) {
            $unusedHumans->getDeposit()->setAmount($unusedHumans->getDeposit()->getAmount() + $birth);
        }
    }

}