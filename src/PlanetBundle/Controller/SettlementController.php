<?php

namespace PlanetBundle\Controller;

use AppBundle\Builder\PlanetBuilder;
use AppBundle\Descriptor\Adapters;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Entity\Human;
use AppBundle\Entity\Human\Event;
use AppBundle\Entity\Human\EventDataTypeEnum;
use AppBundle\Entity\Human\EventTypeEnum;
use AppBundle\Entity\Human\SettlementTitle;
use AppBundle\PlanetConnection\DynamicPlanetConnector;
use PlanetBundle\Entity;
use AppBundle\Repository\JobRepository;
use PlanetBundle\Repository\RegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Tracy\Debugger;

/**
 * @Route(path="settlement")
 */
class SettlementController extends BasePlanetController
{
	/**
	 * @Route("/{settlement}/dashboard", name="settlement_dashboard")
	 */
	public function dashboardAction(Entity\Settlement $settlement, Request $request)
	{
        /** @var PlanetBuilder $builder */
        $builder = $this->get('planet_builder');
        $blueprintsByRegions = [];
        /** @var Entity\Region $region */
        foreach ($settlement->getRegions() as $region) {
            // TODO: zmenit na aktualne prihlaseneho cloveka
            $blueprintsByRegions[$region->getCoords()] = $builder->getAvailableBlueprints($region, $settlement->getManager());
        }

        return $this->render('Settlement/dashboard.html.twig', [
            'settlement' => $settlement,
            'buildingBlueprints' => $blueprintsByRegions,
            'human' => $settlement->getManager(),
            'foodConsumption' => $this->get('maintainer_food')->getFoodConsumptionEstimation($settlement),
            'populationChanges' => $this->get('maintainer_population')->getBirths($settlement),
        ]);
	}

    /**
     * @Route("/{settlement}/warehouses", name="settlement_warehouses")
     */
    public function warehouseContentAction(Entity\Settlement $settlement, Request $request)
    {
        $warehouses = Adapters\Warehouse::in($settlement);
        $portables = Adapters\Portable::in($settlement);

        return $this->render('Settlement/warehouse-content-fragment.html.twig', [
            'settlement' => $settlement,
            'resources' => $portables,
            'warehouses' => $warehouses,
            'human' => $this->getHuman(),
        ]);
    }

    /**
     * @Route("/{settlement}/buildings", name="settlement_buildings")
     */
    public function buildingsAction(Entity\Settlement $settlement, Request $request)
    {
        $buildings = [];
        foreach ($settlement->getResourceDeposits() as $deposit) {
            if (($building = $deposit->asUseCase(UseCaseEnum::LAND_BUILDING)) != null) {
                $buildings[] = $building;
            }
        }

        return $this->render('Settlement/buildings-fragment.html.twig', [
            'settlement' => $settlement,
            'buildings' => $buildings,
            'human' => $this->getHuman(),
        ]);
    }

    /**
     * @Route("/{settlement}/housing", name="settlement_housing")
     */
    public function housingAction(Entity\Settlement $settlement, Request $request)
    {
        /** @var Adapters\LivingBuilding[] $houses */
        $houses = Adapters\LivingBuilding::in($settlement);
        $peopleCount = 0;
        $peopleBirths = 0;
        foreach (Adapters\People::in($settlement) as $people) {
            $peopleCount += $people->getPeopleCount();
        }
        foreach ($this->get('maintainer_population')->getBirths($settlement) as $birthCount) {
            $peopleBirths += $birthCount;
        }

        $foods = Adapters\BasicFood::in($settlement);

        return $this->render('Settlement/housing.html.twig', [
            'settlement' => $settlement,
            'people' => $peopleCount,
            'peopleBirths' => $peopleBirths,
            'foods' => $foods,
            'foodEnergy' => Adapters\BasicFood::countEnergy($foods),
            'foodVariety' => Adapters\BasicFood::countVariety($this->get('maintainer_food')->getFoodConsumptionEstimation($settlement)),
            'housingCapacity' => Adapters\LivingBuilding::countLivingCapacity($houses),
            'houses' => $houses,
            'human' => $this->getHuman(),
        ]);
    }

    /**
     * @Route("/teams/{regionC}_{regionL}_{regionR}", name="settlement_teams")
     */
    public function teamsAction(Entity\Peak $regionC, Entity\Peak $regionL, Entity\Peak $regionR, Request $request)
    {
        /** @var Entity\Region $region */
        $region = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Region::class)->findByPeaks($regionC, $regionL, $regionR);
        $transporterBlueprints = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::TEAM_TRANSPORTERS);
        $builderBlueprints = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::TEAM_BUILDERS);
        $merchantBlueprints = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::TEAM_MERCHANTS);
        $scientistBlueprints = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::TEAM_SCIENTISTS);
        $workerBlueprints = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::TEAM_WORKERS);
        $farmerBlueprints = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::TEAM_FARMERS);
        $armyBlueprints = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::TEAM_SOLDIERS);

        /** @var Adapters\LivingBuilding[] $teams */
        $teams = Adapters\Team::in($region);
        $peopleCount = $region->getPeopleCount();
        $employees = Adapters\Team::countPeople($teams);

        return $this->render('Settlement/teams.html.twig', [
            'settlement' => $region->getSettlement(),
            'region' => $region,
            'people' => $peopleCount,
            'unemployedPeople' => $peopleCount - $employees,
            'transporters' => Adapters\TeamTransporter::in($region),
            'builders' => Adapters\TeamBuilder::in($region),
            'merchants' => Adapters\TeamMerchant::in($region),
            'scientists' => Adapters\TeamScientist::in($region),
            'workers' => Adapters\TeamWorker::in($region),
            'farmers' => Adapters\TeamFarmer::in($region),
            'army' => Adapters\TeamArmy::in($region),
            'transporterBlueprints' => $transporterBlueprints,
            'builderBlueprints' => $builderBlueprints,
            'merchantBlueprints' => $merchantBlueprints,
            'scientistBlueprints' => $scientistBlueprints,
            'workerBlueprints' => $workerBlueprints,
            'farmerBlueprints' => $farmerBlueprints,
            'armyBlueprints' => $armyBlueprints,
        ]);
    }

    /**
     * @Route("/create-team/{regionC}_{regionL}_{regionR}/{blueprint}", name="settlement_team_create")
     */
    public function createTeamAction(Entity\Peak $regionC, Entity\Peak $regionL, Entity\Peak $regionR, Entity\Blueprint $blueprint, Request $request)
    {
        /** @var Entity\Region $region */
        $region = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Region::class)->findByPeaks($regionC, $regionL, $regionR);
        $region->addResourceDeposit($blueprint, 1);
        $this->getDoctrine()->getManager('planet')->persist($region);
        $this->getDoctrine()->getManager('planet')->flush();
        return $this->redirectToRoute('settlement_jobs', [
            'settlement' => $region->getSettlement()->getId(),
        ]);
    }

    /**
     * @Route("/connectableRegions/{settlement}", name="settlement_connectable_regions")
     */
    public function connectableRegionsAction(Entity\Settlement $settlement, Request $request)
    {
        $regions = [];
        /** @var Entity\Region $settledRegion */
        foreach ($settlement->getRegions() as $settledRegion) {
            /** @var RegionRepository $repo */
            $repo = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Region::class);
            $nears = $repo->getRegionNeighbourhood($settledRegion);
            /** @var Entity\Region $near */
            foreach ($nears as $near) {
                $regions[$near->getCoords()] = $near;
            }
        }

        foreach ($settlement->getRegions() as $settledRegion) {
            unset($regions[$settledRegion->getCoords()]);
        }

        return $this->render('Settlement/connect-regions.html.twig', [
            'human' => $this->getHuman(),
            'settlement' => $settlement,
            'nearRegions' => $regions,
        ]);
    }

    /**
     * @Route("/{settlement}/jobs", name="settlement_jobs")
     */
    public function jobsAction(Entity\Settlement $settlement, Request $request)
    {
        /** @var \PlanetBundle\Repository\JobRepository $jobRepo */
        $jobRepo = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Job\ProduceJob::class);

        return $this->render('Settlement/jobs.html.twig', [
            'human' => $this->getHuman(),
            'settlement' => $settlement,
            'administrationJobs' => $jobRepo->getAdministrationBySettlement($settlement),
            'buildJobs' => $jobRepo->getBuildBySettlement($settlement),
            'transportJobs' => $jobRepo->getTransportBySettlement($settlement),
            'produceJobs' => $jobRepo->getProduceBySettlement($settlement),
            'buyJobs' => $jobRepo->getBuyBySettlement($settlement),
            'sellJobs' => $jobRepo->getSellBySettlement($settlement),
        ]);
    }

    /**
     * @Route("/connectRegions/{settlement}/{regionC}_{regionL}_{regionR}", name="settlement_connect_regions")
     */
    public function connectRegionsAction(Entity\Settlement $settlement, Entity\Peak $regionC, Entity\Peak $regionL, Entity\Peak $regionR, Request $request)
    {
        $region = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Region::class)->findByPeaks($regionC, $regionL, $regionR);
        $region->setSettlement($settlement);
        $this->getDoctrine()->getManager('planet')->persist($region);
        $this->getDoctrine()->getManager('planet')->persist($settlement);
        $this->getDoctrine()->getManager('planet')->flush();

        $this->createEvent(EventTypeEnum::SETTLEMENT_EXPAND, [
            EventDataTypeEnum::REGION => $region,
        ]);

        return $this->redirectToRoute('settlement_dashboard', [
            'settlement' => $settlement->getId(),
        ]);
    }

    /**
     * @Route("/fastBuild/{project}", name="settlement_fast_build")
     */
    public function supervisedFastBuildAction(Entity\CurrentBuildingProject $project, Request $request)
    {
        $settlement = $project->getRegion()->getSettlement();
        $builder = $this->get('planet_builder');
        $builder->buildProjectStep($project);
        if ($project->isDone()) {
            $builder->buildProject($project);
            $this->getDoctrine()->getManager('planet')->remove($project);
        } else {
            $this->getDoctrine()->getManager('planet')->persist($project);
        }
        $this->getDoctrine()->getManager('planet')->flush();


        $this->createEvent(EventTypeEnum::SETTLEMENT_BUILD, [
            EventDataTypeEnum::BLUEPRINT => $project->getBuildingBlueprint()->getDescription(),
        ]);

        return $this->redirectToRoute('settlement_dashboard', [
            'settlement' => $settlement->getId(),
        ]);
    }

    /**
     * @Route("/changeType/{settlement}to{blueprint}", name="settlement_change_type")
     */
    public function changeTypeAction(Entity\Settlement $settlement, Entity\Blueprint $blueprint, Request $request)
    {
        $settlement->setType($blueprint->getResourceDescriptor());
        $this->getDoctrine()->getManager('planet')->persist($settlement);
        $this->getDoctrine()->getManager('planet')->flush();

        $this->createEvent(EventTypeEnum::SETTLEMENT_ADMINISTRATIVE_CHANGE, [
            EventDataTypeEnum::BLUEPRINT => $blueprint,
        ]);

        return $this->redirectToRoute('settlement_dashboard', [
            'settlement' => $settlement->getId(),
        ]);
    }

    /**
     * @Route("/cut-to-half/{settlement}", name="settlement_cut")
     */
    public function cutOutHalfAction(Entity\Settlement $settlement) {
        $remainsRegions = [];
        $transferRegions = [];
        $count = count($settlement->getRegions());
        foreach ($settlement->getRegions() as $region) {
            if ($count-- < count($settlement->getRegions())) {
                $transferRegions[] = $region;
            } else {
                $remainsRegions[] = $region;
            }
        }
        $settlement->setRegions($remainsRegions);

        $newHalf = new Entity\Settlement();
        $newHalf->setRegions($transferRegions);
        /** @var Entity\Region $firstRegion */
        $firstRegion = array_pop($transferRegions);
        // FIXME: zkontrolovat jestli se vrchol uz nepouziva jako nejake administrativni centrum
        $newHalf->setAdministrativeCenter($firstRegion->getPeakCenter());
        $newHalf->setOwner($settlement->getOwner());
        $newHalf->setManager($settlement->getOwner());
        $newHalf->setType($settlement->getType());

        foreach ($transferRegions as $transferRegion) {
            $transferRegion->setSettlement($newHalf);
            $this->get('doctrine.orm.planet_entity_manager')->persist($transferRegion);
        }

        $this->get('doctrine.orm.planet_entity_manager')->persist($settlement);
        $this->get('doctrine.orm.planet_entity_manager')->persist($newHalf);
        $this->get('doctrine.orm.planet_entity_manager')->flush();

        /** @var Human $globalHuman */
        $globalHuman = $this->get('doctrine.orm.entity_manager')->find(Human::class, $settlement->getOwner()->getGlobalHumanId());
        $protectorTitle = new SettlementTitle();
        $protectorTitle->setName('Protector of '.$newHalf->getName());
        $protectorTitle->setHumanHolder($globalHuman);
        $protectorTitle->setTransferSettings([
            'inheritance' => 'primogeniture',
        ]);
        $protectorTitle->setSettlementId($newHalf->getId());
        $protectorTitle->setSettlementPlanet(DynamicPlanetConnector::$PLANET);
        $globalHuman->addTitle($protectorTitle);
        $this->get('doctrine.orm.entity_manager')->persist($protectorTitle);
        $this->get('doctrine.orm.entity_manager')->persist($globalHuman);
        $this->get('doctrine.orm.entity_manager')->flush();

        return $this->redirectToRoute('settlement_dashboard', [
            'settlement' => $settlement->getId(),
        ]);
    }
}
