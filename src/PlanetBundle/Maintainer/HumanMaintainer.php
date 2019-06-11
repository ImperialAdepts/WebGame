<?php

namespace PlanetBundle\Maintainer;

use AppBundle\Descriptor\Adapters\BasicFood;
use AppBundle\Descriptor\Adapters\Team;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Entity\Human;
use AppBundle\Repository\HumanRepository;
use PlanetBundle\Entity\Region;
use AppBundle\Entity\ResourceDeposit;
use Doctrine\ORM\EntityManager;

class HumanMaintainer
{
    // magick number, later will be calculated by genom, gravity and planet rotation, etc
    const HUMAN_WORK_HOURS_BY_PHASE = 5000;

    /** @var HumanRepository */
    private $generalHumanRepository;

    /**
     * HumanMaintainer constructor.
     * @param HumanRepository $generalHumanRepository
     */
    public function __construct(HumanRepository $generalHumanRepository)
    {
        $this->generalHumanRepository = $generalHumanRepository;
    }


    public function addHumanHours() {
        /** @var Human[] $exaustedHumans */
        $exaustedHumans = $this->generalHumanRepository->findAll();
        foreach ($exaustedHumans as $human) {
            $newWorkHours = $human->getHours() + self::HUMAN_WORK_HOURS_BY_PHASE;
            if ($newWorkHours > self::HUMAN_WORK_HOURS_BY_PHASE) {
                $human->setHours(self::HUMAN_WORK_HOURS_BY_PHASE);
            } else {
                $human->setHours($newWorkHours);
            }
        }
    }

    public function resetFeelings() {
        /** @var Human[] $humans */
        $humans = $this->generalHumanRepository->findAll();
        foreach ($humans as $human) {
            $historyCount = 0;
            foreach ($human->getFeelings()->getHistory() as $feelingChange) {
                $historyCount += $feelingChange->getChange();
            }
            $human->getFeelings()->setLastPeriodHappiness($historyCount);
            $human->getFeelings()->setLastPeriodSadness($historyCount);
            $human->getFeelings()->setThisTimeHappiness(0);
            $human->getFeelings()->setThisTimeSadness(0);
        }
    }
}