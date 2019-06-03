<?php
namespace PlanetBundle\Maintainer;

use AppBundle\Builder\PlanetBuilder;
use AppBundle\Descriptor\Adapters\Team;
use AppBundle\Descriptor\TimeTransformator;
use AppBundle\Entity\Human;
use AppBundle\Entity\Human\Event;
use AppBundle\Entity\SolarSystem\Planet;
use Doctrine\Common\Persistence\ObjectManager;
use PlanetBundle\Entity as PlanetEntity;

class PlanetMaintainer
{
    /** @var ObjectManager */
    private $generalEntityManager;

    /** @var ObjectManager */
    private $planetEntityManager;

    /** @var Planet */
    private $planet;

    /** @var PopulationMaintainer */
    private $populationMaintainer;

    /** @var FoodMaintainer */
    private $foodMaintainer;

    /** @var PlanetBuilder */
    private $planetBuilder;

    /** @var JobMaintainer */
    private $jobMaintainer;

    /** @var HumanMaintainer */
    private $humanMaintainer;

    /** @var Maintainer */
    private $maintainer;

    /**
     * PlanetMaintainer constructor.
     * @param ObjectManager $generalEntityManager
     * @param ObjectManager $planetEntityManager
     * @param Planet $planet
     */
    public function __construct(ObjectManager $generalEntityManager, ObjectManager $planetEntityManager, Planet $planet)
    {
        $this->generalEntityManager = $generalEntityManager;
        $this->planetEntityManager = $planetEntityManager;
        $this->planet = $planet;
        $this->populationMaintainer = new PopulationMaintainer($planetEntityManager);
        $this->foodMaintainer = new FoodMaintainer($planetEntityManager);
        $this->planetBuilder = new PlanetBuilder($planetEntityManager, []);
        $this->jobMaintainer = new JobMaintainer($generalEntityManager, $planetEntityManager, $planet);
        $this->humanMaintainer = new HumanMaintainer($generalEntityManager);
        $this->maintainer = new Maintainer($planetEntityManager, $this->foodMaintainer, $this->populationMaintainer);
    }

    public function goToNewPlanetPhase() {
        if ($this->planet->getLastPhaseUpdate() == null) {
            $this->switchPhases();
        }

        $this->doPlanedBuildingProjects();
        $this->doEndPhaseJobs();
        $this->giveHumanRelationshipFeelings();
        $this->clear();
        $this->switchPhases();
        $this->maintainWorkhours();
        $this->doBirths();
        $this->doStartPhaseJobs();

        $this->planetEntityManager->flush();
        $this->generalEntityManager->flush();
    }

    private function doStartPhaseJobs()
    {
        $settlements = $this->planetEntityManager->getRepository(PlanetEntity\Settlement::class)->getAll();

        /** @var PlanetEntity\Settlement $settlement */
        foreach ($settlements as $settlement) {
            $jobsCount = 0;
            $jobsCountByType = [];

            /** @var PlanetEntity\Job\AdministrationJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\AdministrationJob::class)->getAdministrationBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_START);

            $jobsCountByType[PlanetEntity\Job\AdministrationJob::class] = 0;
            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
                $jobsCount++;
                $jobsCountByType[PlanetEntity\Job\AdministrationJob::class] += 1;
            }

            /** @var PlanetEntity\Job\ProduceJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\ProduceJob::class)->getProduceBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_START);

            $jobsCountByType[PlanetEntity\Job\ProduceJob::class] = 0;
            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
                $jobsCount++;
                $jobsCountByType[PlanetEntity\Job\ProduceJob::class] += 1;
            }

            /** @var PlanetEntity\Job\BuildJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\BuildJob::class)->getBuildBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_START);

            $jobsCountByType[PlanetEntity\Job\BuildJob::class] = 0;
            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
                $jobsCount++;
                $jobsCountByType[PlanetEntity\Job\BuildJob::class] += 1;
            }

            /** @var PlanetEntity\Job\SellJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\SellJob::class)->getSellBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_START);

            $jobsCountByType[PlanetEntity\Job\SellJob::class] = 0;
            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
                $jobsCount++;
                $jobsCountByType[PlanetEntity\Job\SellJob::class] += 1;
            }

            /** @var PlanetEntity\Job\BuyJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\BuyJob::class)->getBuyBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_START);

            $jobsCountByType[PlanetEntity\Job\BuyJob::class] = 0;
            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
                $jobsCount++;
                $jobsCountByType[PlanetEntity\Job\BuyJob::class] += 1;
            }

            /** @var PlanetEntity\Job\TransportJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\TransportJob::class)->getTransportBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_START);

            $jobsCountByType[PlanetEntity\Job\TransportJob::class] = 0;
            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
                $jobsCount++;
                $jobsCountByType[PlanetEntity\Job\TransportJob::class] += 1;
            }

            $globalHuman = $this->generalEntityManager->getRepository(Human::class)->find($settlement->getManager()->getGlobalHumanId());
            $this->createEvent('PRE_'.Human\EventTypeEnum::JOB_DONE, $globalHuman, [
                Human\EventTypeEnum::SETTLEMENT => $settlement->getId(),
                'jobs_count' => $jobsCount,
                'jobs_type' => $jobsCountByType,
            ]);
        }
    }

    private function doEndPhaseJobs()
    {
        $settlements = $this->planetEntityManager->getRepository(PlanetEntity\Settlement::class)->getAll();

        /** @var PlanetEntity\Settlement $settlement */
        foreach ($settlements as $settlement) {
            $jobsCount = 0;
            $jobsCountByType = [];

            /** @var PlanetEntity\Job\AdministrationJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\AdministrationJob::class)->getAdministrationBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_END);

            $jobsCountByType[PlanetEntity\Job\AdministrationJob::class] = 0;
            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
                $jobsCount++;
                $jobsCountByType[PlanetEntity\Job\AdministrationJob::class] += 1;
            }

            /** @var PlanetEntity\Job\ProduceJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\ProduceJob::class)->getProduceBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_END);

            $jobsCountByType[PlanetEntity\Job\ProduceJob::class] = 0;
            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
                $jobsCount++;
                $jobsCountByType[PlanetEntity\Job\ProduceJob::class] += 1;
            }

            /** @var PlanetEntity\Job\BuildJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\BuildJob::class)->getBuildBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_END);

            $jobsCountByType[PlanetEntity\Job\BuildJob::class] = 0;
            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
                $jobsCount++;
                $jobsCountByType[PlanetEntity\Job\BuildJob::class] += 1;
            }

            /** @var PlanetEntity\Job\SellJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\SellJob::class)->getSellBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_END);

            $jobsCountByType[PlanetEntity\Job\SellJob::class] = 0;
            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
                $jobsCount++;
                $jobsCountByType[PlanetEntity\Job\SellJob::class] += 1;
            }

            /** @var PlanetEntity\Job\BuyJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\BuyJob::class)->getBuyBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_END);

            $jobsCountByType[PlanetEntity\Job\BuyJob::class] = 0;
            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
                $jobsCount++;
                $jobsCountByType[PlanetEntity\Job\BuyJob::class] += 1;
            }

            /** @var PlanetEntity\Job\TransportJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\TransportJob::class)->getTransportBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_END);

            $jobsCountByType[PlanetEntity\Job\TransportJob::class] = 0;
            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
                $jobsCount++;
                $jobsCountByType[PlanetEntity\Job\TransportJob::class] += 1;
            }

            $globalHuman = $this->generalEntityManager->getRepository(Human::class)->find($settlement->getManager()->getGlobalHumanId());
            $this->createEvent('POST_'.Human\EventTypeEnum::JOB_DONE, $globalHuman, [
                Human\EventTypeEnum::SETTLEMENT => $settlement->getId(),
                'jobs_count' => $jobsCount,
                'jobs_type' => $jobsCountByType,
            ]);
        }
    }

    private function switchPhases()
    {
        if ($this->planet->getLastPhaseUpdate() === null) {
            $this->planet->setLastPhaseUpdate(TimeTransformator::timestampToPhase($this->planet, time()));
        } else {
            $this->planet->setLastPhaseUpdate($this->planet->getLastPhaseUpdate()+1);
        }
        $this->planet->setNextUpdateTime(TimeTransformator::phaseToTimestamp($this->planet, $this->planet->getLastPhaseUpdate()+1));

        $this->generalEntityManager->persist($this->planet);
    }

    private function doBirths()
    {
        $settlements = $this->planetEntityManager->getRepository(PlanetEntity\Settlement::class)->getAll();

        /** @var PlanetEntity\Settlement $settlement */
        foreach ($settlements as $settlement) {
            $settlementPopulationIncrease = 0;
            $regions = [];
            /** @var PlanetEntity\Region $region */
            foreach ($settlement->getRegions() as $region) {
                $regions[$region->getCoords()] = $this->populationMaintainer->getBirths($region);
                foreach ($this->populationMaintainer->getBirths($region) as $birth) {
                    $settlementPopulationIncrease += $birth;
                }

                $this->populationMaintainer->doBirths($region);
                $this->planetEntityManager->persist($region);
            }
            $peaks = [];
            /** @var PlanetEntity\Peak $peak */
            foreach ($settlement->getPeaks() as $peak) {
                $peaks[$peak->getId()] = $this->populationMaintainer->getBirths($peak);
                foreach ($this->populationMaintainer->getBirths($peak) as $birth) {
                    $settlementPopulationIncrease += $birth;
                }
                $this->populationMaintainer->doBirths($peak);
                $this->planetEntityManager->persist($peak);
            }
            $globalHuman = $this->generalEntityManager->getRepository(Human::class)->find($settlement->getManager()->getGlobalHumanId());
            $this->createEvent(Human\EventTypeEnum::SETTLEMENT_PEOPLE_BORN, $globalHuman, [
                Human\EventDataTypeEnum::POPULATION_CHANGE => $settlementPopulationIncrease,
                Human\EventDataTypeEnum::REGIONS => $regions,
                Human\EventDataTypeEnum::PEAKS => $peaks,
            ]);
        }
    }

    private function maintainWorkhours() {
        $settlements = $this->planetEntityManager->getRepository(PlanetEntity\Settlement::class)->getAll();

        /** @var PlanetEntity\Settlement $settlement */
        foreach ($settlements as $settlement) {
            /** @var PlanetEntity\Region $region */
            foreach ($settlement->getRegions() as $region) {
                $this->foodMaintainer->eatFood($region);

                /** @var Team $team */
                foreach (Team::in($region) as $team) {
                    $team->getDeposit()->setWorkHours(24*365);
                    $this->planetEntityManager->persist($team->getDeposit());
                }
            }
        }

        $this->humanMaintainer->addHumanHours();
    }

    private function doPlanedBuildingProjects()
    {
        $projects = $this->planetEntityManager->getRepository(PlanetEntity\CurrentBuildingProject::class)->getActiveSortedByPriority();
        /** @var PlanetEntity\BuildingProject $project */
        foreach ($projects as $project) {
            $this->builder->buildProjectStep($project);
            if ($project->isDone()) {
                $this->builder->buildProject($project);
                $this->planetEntityManager->remove($project);
            } else {
                $this->planetEntityManager->persist($project);
            }
        }
    }

    private function clear()
    {
        $this->maintainer->clearEmptyDeposits();
        $this->humanMaintainer->resetFeelings();
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

    private function giveHumanRelationshipFeelings()
    {
        // TODO: zapocitat radost z progresu pratel a smutek z progresu rivalu
    }
}