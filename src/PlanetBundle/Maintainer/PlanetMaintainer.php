<?php
namespace PlanetBundle\Maintainer;

use AppBundle\Builder\PlanetBuilder;
use AppBundle\Descriptor\Adapters\Team;
use AppBundle\Descriptor\TimeTransformator;
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
            /** @var PlanetEntity\Job\AdministrationJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\AdministrationJob::class)->getAdministrationBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_START);

            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
            }

            /** @var PlanetEntity\Job\ProduceJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\ProduceJob::class)->getProduceBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_START);

            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
            }

            /** @var PlanetEntity\Job\BuildJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\BuildJob::class)->getBuildBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_START);

            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
            }

            /** @var PlanetEntity\Job\SellJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\SellJob::class)->getSellBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_START);

            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
            }

            /** @var PlanetEntity\Job\BuyJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\BuyJob::class)->getBuyBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_START);

            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
            }

            /** @var PlanetEntity\Job\TransportJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\TransportJob::class)->getTransportBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_START);

            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
            }
        }
    }

    private function doEndPhaseJobs()
    {
        $settlements = $this->planetEntityManager->getRepository(PlanetEntity\Settlement::class)->getAll();

        /** @var PlanetEntity\Settlement $settlement */
        foreach ($settlements as $settlement) {
            /** @var PlanetEntity\Job\AdministrationJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\AdministrationJob::class)->getAdministrationBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_END);

            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
            }

            /** @var PlanetEntity\Job\ProduceJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\ProduceJob::class)->getProduceBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_END);

            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
            }

            /** @var PlanetEntity\Job\BuildJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\BuildJob::class)->getBuildBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_END);

            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
            }

            /** @var PlanetEntity\Job\SellJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\SellJob::class)->getSellBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_END);

            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
            }

            /** @var PlanetEntity\Job\BuyJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\BuyJob::class)->getBuyBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_END);

            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
            }

            /** @var PlanetEntity\Job\TransportJob $jobs */
            $jobs = $this->planetEntityManager->getRepository(PlanetEntity\Job\TransportJob::class)->getTransportBySettlement($settlement, PlanetEntity\Job\JobTriggerTypeEnum::PHASE_END);

            foreach ($jobs as $job) {
                $this->jobMaintainer->run($job);
            }
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
            /** @var PlanetEntity\Region $region */
            foreach ($settlement->getRegions() as $region) {
                $this->populationMaintainer->doBirths($region);
                $this->planetEntityManager->persist($region);
            }
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
}