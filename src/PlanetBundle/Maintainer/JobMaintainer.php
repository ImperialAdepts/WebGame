<?php
namespace PlanetBundle\Maintainer;

use AppBundle\Entity\Blueprint;
use AppBundle\Entity\Human;
use AppBundle\Entity\Human\Event;
use AppBundle\Entity\Job\ProduceJob;
use AppBundle\Entity\SolarSystem\Planet;
use AppBundle\Repository\HumanRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use PlanetBundle\Entity\Job\Job;

class JobMaintainer
{
    /** @var HumanRepository */
    private $generalHumanRepository;

    /**
     * JobMaintainer constructor.
     * @param HumanRepository $generalHumanRepository
     */
    public function __construct(HumanRepository $generalHumanRepository)
    {
        $this->generalHumanRepository = $generalHumanRepository;
    }

    public function run(Job $job) {
        $globalSupervisor = null;
        if ($job->getSupervisor() !== null) {
            $globalSupervisor = $this->generalHumanRepository->findOneBy([
                'id' => $job->getSupervisor()->getId(),
            ]);
        }
        if ($globalSupervisor == null) {
            $globalSupervisor = $this->generalHumanRepository->findOneBy([
                'id' => $job->getRegion()->getSettlement()->getOwner()->getId()
            ]);
        }
    }

    /**
     * @param Job $job
     * @return int
     */
    public function getSupervisedWorkLenght(Job $job) {
        return 10;
    }
}