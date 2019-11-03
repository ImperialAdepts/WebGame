<?php
namespace PlanetBundle\Maintainer;

use AppBundle\Builder\PlanetBuilder;
use AppBundle\Descriptor\Adapters\Team;
use AppBundle\Descriptor\TimeTransformator;
use AppBundle\Entity\Human;
use AppBundle\PlanetConnection\DynamicPlanetConnector;
use AppBundle\Repository\Human\AchievementRepository;
use AppBundle\Repository\HumanRepository;
use Doctrine\Common\Persistence\ObjectManager;
use PlanetBundle\Builder\EventBuilder;
use PlanetBundle\Entity as PlanetEntity;
use PlanetBundle\Repository\SettlementRepository;
use Tracy\Debugger;

class PlanetMaintainer
{
    /** @var ObjectManager */
    private $generalEntityManager;
    /** @var ObjectManager */
    private $planetEntityManager;

    /** @var DynamicPlanetConnector */
    private $plannetConnection;

    /** @var HumanRepository */
    private $generalHumanRepository;

    /** @var SettlementRepository */
    private $settlementRepository;

    /** @var AchievementRepository */
    private $achievementRepository;

    /** @var EventBuilder */
    private $eventBuilder;

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

    /** @var LifeMaintainer */
    private $lifeMaintainer;

    /**
     * PlanetMaintainer constructor.
     * @param ObjectManager $generalEntityManager
     * @param ObjectManager $planetEntityManager
     * @param DynamicPlanetConnector $plannetConnection
     * @param HumanRepository $generalHumanRepository
     * @param SettlementRepository $settlementRepository
     * @param AchievementRepository $achievementRepository
     * @param EventBuilder $eventBuilder
     * @param PopulationMaintainer $populationMaintainer
     * @param FoodMaintainer $foodMaintainer
     * @param PlanetBuilder $planetBuilder
     * @param JobMaintainer $jobMaintainer
     * @param HumanMaintainer $humanMaintainer
     * @param Maintainer $maintainer
     * @param LifeMaintainer $lifeMaintainer
     */
    public function __construct(ObjectManager $generalEntityManager, ObjectManager $planetEntityManager, DynamicPlanetConnector $plannetConnection, HumanRepository $generalHumanRepository, SettlementRepository $settlementRepository, AchievementRepository $achievementRepository, EventBuilder $eventBuilder, PopulationMaintainer $populationMaintainer, FoodMaintainer $foodMaintainer, PlanetBuilder $planetBuilder, JobMaintainer $jobMaintainer, HumanMaintainer $humanMaintainer, Maintainer $maintainer, LifeMaintainer $lifeMaintainer)
    {
        $this->generalEntityManager = $generalEntityManager;
        $this->planetEntityManager = $planetEntityManager;
        $this->plannetConnection = $plannetConnection;
        $this->generalHumanRepository = $generalHumanRepository;
        $this->settlementRepository = $settlementRepository;
        $this->achievementRepository = $achievementRepository;
        $this->eventBuilder = $eventBuilder;
        $this->populationMaintainer = $populationMaintainer;
        $this->foodMaintainer = $foodMaintainer;
        $this->planetBuilder = $planetBuilder;
        $this->jobMaintainer = $jobMaintainer;
        $this->humanMaintainer = $humanMaintainer;
        $this->maintainer = $maintainer;
        $this->lifeMaintainer = $lifeMaintainer;
    }


    private function getPlanet() {
        return $this->plannetConnection->getPlanet();
    }


    public function goToNewPlanetPhase() {
        if ($this->getPlanet()->getLastPhaseUpdate() == null) {
            $this->switchPhases();
        }

        $this->doPlanedBuildingProjects();
        $this->doEndPhaseJobs();
        $this->giveHumanRelationshipFeelings();
        $this->killPeopleByAge();
        $this->countStatisticsAndAchievements();
        $this->clear();
        $this->switchPhases();
        $this->maintainWorkhours();
        $this->doPopulationBirths();
        $this->doNPCBirths();
        $this->doStartPhaseJobs();
    }

    private function doStartPhaseJobs()
    {
        $settlements = $this->settlementRepository->getAll();

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
            $this->eventBuilder->create('PRE_'.Human\EventTypeEnum::JOB_DONE, $globalHuman, [
                Human\EventTypeEnum::SETTLEMENT => $settlement->getId(),
                'jobs_count' => $jobsCount,
                'jobs_type' => $jobsCountByType,
            ]);
        }
    }

    private function doEndPhaseJobs()
    {
        $settlements = $this->settlementRepository->getAll();

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
            $this->eventBuilder->create('POST_'.Human\EventTypeEnum::JOB_DONE, $globalHuman, [
                Human\EventTypeEnum::SETTLEMENT => $settlement->getId(),
                'jobs_count' => $jobsCount,
                'jobs_type' => $jobsCountByType,
            ]);
        }
    }

    private function switchPhases()
    {
        if ($this->getPlanet()->getLastPhaseUpdate() === null) {
            $this->getPlanet()->setLastPhaseUpdate(TimeTransformator::timestampToPhase($this->getPlanet(), time()));
        } else {
            $this->getPlanet()->setLastPhaseUpdate($this->getPlanet()->getLastPhaseUpdate()+1);
        }
        $this->getPlanet()->setNextUpdateTime(TimeTransformator::phaseToTimestamp($this->getPlanet(), $this->getPlanet()->getLastPhaseUpdate()+1));

        $this->generalEntityManager->persist($this->getPlanet());
    }

    private function doPopulationBirths()
    {
        $settlements = $this->settlementRepository->getAll();

        /** @var PlanetEntity\Settlement $settlement */
        foreach ($settlements as $settlement) {
            $settlementPopulationIncrease = 0;
            /** @var PlanetEntity\Deposit $deposit */
            foreach ($settlement->getDeposits() as $deposit) {
                foreach ($this->populationMaintainer->getBirths($deposit) as $birth) {
                    $settlementPopulationIncrease += $birth;
                }

                $this->populationMaintainer->doBirths($deposit);
                $this->planetEntityManager->persist($deposit);
            }
            $globalHuman = $this->generalHumanRepository->find($settlement->getManager()->getGlobalHumanId());
            $this->eventBuilder->create(Human\EventTypeEnum::SETTLEMENT_PEOPLE_BORN, $globalHuman, [
                Human\EventDataTypeEnum::POPULATION_CHANGE => $settlementPopulationIncrease,
            ]);
        }
    }

    private function doNPCBirths()
    {
        $settlements = $this->settlementRepository->getAll();

        /** @var PlanetEntity\Settlement $settlement */
        foreach ($settlements as $settlement) {
            $freeNPCCapacity = 3;
            /** @var PlanetEntity\Region $region */
            foreach ($settlement->getRegions() as $region) {
                $freeNPCCapacity += $region->getNPCCapacity();
            }

            $aliveHumans = [];
            /** @var PlanetEntity\Peak $peak */
            foreach ($settlement->getPeaks() as $peak) {
                $freeNPCCapacity += $peak->getNPCCapacity();
                foreach ($peak->getHumans() as $human) {
                    /** @var Human $globalHuman */
                    $globalHuman = $this->generalHumanRepository->find($human->getGlobalHumanId());
                    if ($globalHuman->isAlive()) {
                        $aliveHumans[] = $globalHuman;
                        $freeNPCCapacity--;
                    }
                }
            }

            if ($freeNPCCapacity > 0 && count($aliveHumans) > 0) {
                $probability = 100* $freeNPCCapacity / count($aliveHumans);
                foreach ($aliveHumans as $human) {
                    if (random_int(0, 100) < $probability) {
                        $this->lifeMaintainer->makeOffspring($human);
                    }
                }
            }
        }
    }

    private function maintainWorkhours() {
        $settlements = $this->settlementRepository->getAll();

        /** @var PlanetEntity\Settlement $settlement */
        foreach ($settlements as $settlement) {
            /** @var PlanetEntity\Deposit $deposit */
            foreach ($settlement->getDeposits() as $deposit) {
                $this->foodMaintainer->eatFood($deposit);

                /** @var Team $team */
                foreach ($deposit->filterByUseCase(Team::class) as $team) {
                    $team->getConceptAdapter()->setWorkHours(24*365);
                    $this->planetEntityManager->persist($team);
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

    private function countStatisticsAndAchievements() {
        // TODO: spocitat statistiky
        // TODO: rozdat casove achievementy
        // TODO: odsunout vypocet do vlastniho maintaineru

        /** @var Human $mostHappy */
        $mostHappy = $this->achievementRepository->getMostHappyHuman();
        if ($mostHappy) {
            $happyAch = new Human\Achievement();
            $happyAch->setType(Human\AchievementTypeEnum::MOST_HAPPY);
            $happyAch->setTimeType(Human\AchievementTimeTypeEnum::BY_PHASE);
            $happyAch->setSpaceType(Human\AchievementSpaceTypeEnum::PLANET);
            $happyAch->setPlanet($this->getPlanet());
            $happyAch->setPlanetPhase($this->getPlanet()->getLastPhaseUpdate());
            $happyAch->setHolder($mostHappy);
            $this->generalEntityManager->persist($happyAch);
        }

        /** @var Human $mostSad */
        $mostSad = $this->achievementRepository->getMostSadHuman();
        if ($mostSad) {
            $sadAch = new Human\Achievement();
            $sadAch->setType(Human\AchievementTypeEnum::MOST_SAD);
            $sadAch->setTimeType(Human\AchievementTimeTypeEnum::BY_PHASE);
            $sadAch->setSpaceType(Human\AchievementSpaceTypeEnum::PLANET);
            $sadAch->setPlanet($this->getPlanet());
            $sadAch->setPlanetPhase($this->getPlanet()->getLastPhaseUpdate());
            $sadAch->setHolder($mostSad);
            $this->generalEntityManager->persist($sadAch);
        }

        /** @var Human $mostEmotional */
        $mostEmotional = $this->achievementRepository->getMostEmotionalHuman();
        if ($mostEmotional) {
            $emotionalAch = new Human\Achievement();
            $emotionalAch->setType(Human\AchievementTypeEnum::MOST_EMOTIONAL);
            $emotionalAch->setTimeType(Human\AchievementTimeTypeEnum::BY_PHASE);
            $emotionalAch->setSpaceType(Human\AchievementSpaceTypeEnum::PLANET);
            $emotionalAch->setPlanet($this->getPlanet());
            $emotionalAch->setPlanetPhase($this->getPlanet()->getLastPhaseUpdate());
            $emotionalAch->setHolder($mostEmotional);
            $this->generalEntityManager->persist($emotionalAch);
        }
    }

    private function clear()
    {
        $this->maintainer->clearEmptyDeposits();
        $this->humanMaintainer->resetFeelings();
    }

    private function giveHumanRelationshipFeelings()
    {
        // TODO: zapocitat radost z progresu pratel a smutek z progresu rivalu
    }

    private function killPeopleByAge()
    {
        $humans = $this->generalHumanRepository->findBy(['deathTime' => null]);
        foreach ($humans as $human) {
            $reaperDiceRoll = 1000;
            $reaperDiceRoll = @random_int(0, 10000);
            if ($this->lifeMaintainer->getDeathByAgeProbability($human) >= $reaperDiceRoll) {
                $this->lifeMaintainer->kill($human);
            }
        }
    }
}