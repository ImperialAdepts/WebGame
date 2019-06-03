<?php
namespace PlanetBundle\Maintainer;

use AppBundle\Entity\Blueprint;
use AppBundle\Entity\Human;
use AppBundle\Entity\Human\Event;
use AppBundle\Entity\Job\ProduceJob;
use AppBundle\Entity\SolarSystem\Planet;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use PlanetBundle\Entity\Job\Job;

class JobMaintainer
{
    /** @var EntityManager */
    private $generalEntityManager;

    /** @var EntityManager */
    private $planetEntityManager;

    /** @var Planet */
    private $planet;

    /**
     * JobMaintainer constructor.
     * @param EntityManager $generalEntityManager
     * @param EntityManager $planetEntityManager
     * @param Planet $planet
     */
    public function __construct(EntityManager $generalEntityManager, EntityManager $planetEntityManager, Planet $planet)
    {
        $this->generalEntityManager = $generalEntityManager;
        $this->planetEntityManager = $planetEntityManager;
        $this->planet = $planet;
    }


    public function run(Job $job) {
        $globalSupervisor = null;
        if ($job->getSupervisor() !== null) {
            $globalSupervisor = $this->generalEntityManager->getRepository(Human::class)->findOneBy([
                'id' => $job->getSupervisor()->getId(),
            ]);
        }
        if ($globalSupervisor == null) {
            $globalSupervisor = $this->generalEntityManager->getRepository(Human::class)->findOneBy([
                'id' => $job->getRegion()->getSettlement()->getOwner()->getId()
            ]);
        }
//        try {
//            $this->createEvent(Human\EventTypeEnum::JOB_DONE, $globalSupervisor, [
//                'job' => $job,
//                'jobType' => get_class($job),
//                'region' => $job->getRegion(),
//            ]);
//        } catch (ORMException $e) {
//        }
    }

    /**
     * @param $eventNme
     * @param Human $supervisor
     * @param array $eventData
     * @return Event
     * @throws \Doctrine\ORM\ORMException
     */
    public function createEvent($eventNme, Human $supervisor, array $eventData = []) {
        $event = new Event();
        $event->setDescription($eventNme);
        $event->setPlanet($this->planet);
        $event->setPlanetPhase($this->planet->getLastPhaseUpdate());
        $event->setTime(time());
        $event->setDescriptionData($eventData);
        $event->setHuman($supervisor);

        $this->generalEntityManager->persist($event);
        return $event;
    }

    /**
     * @param Job $job
     * @return int
     */
    public function getSupervisedWorkLenght(Job $job) {
        return 10;
    }
}