<?php

namespace PlanetBundle\Maintainer;

use AppBundle\Descriptor\Adapters\BasicFood;
use AppBundle\Descriptor\Adapters\Team;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Entity\Human;
use PlanetBundle\Entity\Region;
use AppBundle\Entity\ResourceDeposit;
use Doctrine\ORM\EntityManager;

class HumanMaintainer
{
    // magick number, later will be calculated by genom, gravity and planet rotation, etc
    const HUMAN_WORK_HOURS_BY_PHASE = 5000;

    /** @var EntityManager */
    private $entityManager;

    /**
     * HumanMaintainer constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addHumanHours() {
        $exaustedHumans = $this->entityManager->getRepository(Human::class)->findAll();
        foreach ($exaustedHumans as $human) {
            $newWorkHours = $human->getHours() + self::HUMAN_WORK_HOURS_BY_PHASE;
            if ($newWorkHours > self::HUMAN_WORK_HOURS_BY_PHASE) {
                $human->setHours(self::HUMAN_WORK_HOURS_BY_PHASE);
            } else {
                $human->setHours($newWorkHours);
            }
            $this->entityManager->persist($human);
        }
        $this->entityManager->flush();
    }

    public function resetFeelings() {
        $humans = $this->entityManager->getRepository(Human::class)->findAll();
        foreach ($humans as $human) {
            $historyCount = 0;
            foreach ($human->getFeelings()->getHistory() as $feelingChange) {
                $historyCount += $feelingChange->getChange();
            }
            $human->getFeelings()->setLastPeriod($historyCount);
            $human->getFeelings()->setThisTime(0);
            $this->entityManager->persist($human);
        }
        $this->entityManager->flush();
    }
}