<?php

namespace AppBundle\Controller;

use AppBundle\Descriptor\Adapters;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Entity;
use AppBundle\Repository\JobRepository;
use AppBundle\Repository\Planet\RegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Tracy\Debugger;

/**
 * @Route(path="settlement")
 */
class SettlementController extends Controller
{
	/**
	 * @Route("/{settlement}/dashboard", name="settlement_dashboard")
	 */
	public function dashboardAction(Entity\Planet\Settlement $settlement, Request $request)
	{
        /** @var PlanetBuilder $builder */
        $builder = $this->get('planet_builder');
        /** @var Entity\Planet\Region $region */
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
    public function warehouseContentAction(Entity\Planet\Settlement $settlement, Request $request)
    {
        /** @var Entity\Human $human */
        $human = $this->get('logged_user_settings')->getHuman();
        $warehouses = Adapters\Warehouse::in($settlement);
        $portables = Adapters\Portable::in($settlement);

        return $this->render('Settlement/warehouse-content-fragment.html.twig', [
            'settlement' => $settlement,
            'resources' => $portables,
            'warehouses' => $warehouses,
            'human' => $human,
        ]);
    }

    /**
     * @Route("/{settlement}/buildings", name="settlement_buildings")
     */
    public function buildingsAction(Entity\Planet\Settlement $settlement, Request $request)
    {
        /** @var Entity\Human $human */
        $human = $this->get('logged_user_settings')->getHuman();
        $buildings = [];
        foreach ($settlement->getResourceDeposits() as $deposit) {
            if (($building = $deposit->asUseCase(UseCaseEnum::LAND_BUILDING)) != null) {
                $buildings[] = $building;
            }
        }

        return $this->render('Settlement/buildings-fragment.html.twig', [
            'settlement' => $settlement,
            'buildings' => $buildings,
            'human' => $human,
        ]);
    }

    /**
     * @Route("/{settlement}/housing", name="settlement_housing")
     */
    public function housingAction(Entity\Planet\Settlement $settlement, Request $request)
    {
        /** @var Entity\Human $human */
        $human = $this->get('logged_user_settings')->getHuman();

        /** @var Adapters\LivingBuilding[] $houses */
        $houses = Adapters\LivingBuilding::in($settlement);
        $peopleCount = 0;
        $peopleBirths = 0;
        foreach (Adapters\People::in($settlement) as $people) {
            $peopleCount += $people->getPeopleCount();
            $peopleBirths += $this->get('maintainer')->getProduction($settlement, $people->getResourceDescriptor());
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
            'human' => $human,
        ]);
    }

    /**
     * @Route("/teams/{regionC}_{regionL}_{regionR}", name="settlement_teams")
     */
    public function teamsAction(Entity\Planet\Peak $regionC, Entity\Planet\Peak $regionL, Entity\Planet\Peak $regionR, Request $request)
    {
        /** @var Entity\Human $human */
        $human = $this->get('logged_user_settings')->getHuman();
        /** @var Entity\Planet\Region $region */
        $region = $this->getDoctrine()->getRepository(Entity\Planet\Region::class)->findByPeaks($regionC, $regionL, $regionR);
        $transporterBlueprints = $this->getDoctrine()->getManager()->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::TEAM_TRANSPORTERS);
        $builderBlueprints = $this->getDoctrine()->getManager()->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::TEAM_BUILDERS);
        $merchantBlueprints = $this->getDoctrine()->getManager()->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::TEAM_MERCHANTS);
        $scientistBlueprints = $this->getDoctrine()->getManager()->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::TEAM_SCIENTISTS);
        $workerBlueprints = $this->getDoctrine()->getManager()->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::TEAM_WORKERS);
        $farmerBlueprints = $this->getDoctrine()->getManager()->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::TEAM_FARMERS);
        $armyBlueprints = $this->getDoctrine()->getManager()->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::TEAM_SOLDIERS);

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
    public function createTeamAction(Entity\Planet\Peak $regionC, Entity\Planet\Peak $regionL, Entity\Planet\Peak $regionR, Entity\Blueprint $blueprint, Request $request)
    {
        /** @var Entity\Planet\Region $region */
        $region = $this->getDoctrine()->getRepository(Entity\Planet\Region::class)->findByPeaks($regionC, $regionL, $regionR);
        $region->addResourceDeposit($blueprint, 1);
        $this->getDoctrine()->getManager()->persist($region);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('settlement_jobs', [
            'settlement' => $region->getSettlement()->getId(),
        ]);
    }

    /**
     * @Route("/connectableRegions/{settlement}", name="settlement_connectable_regions")
     */
    public function connectableRegionsAction(Entity\Planet\Settlement $settlement, Request $request)
    {
        /** @var Entity\Human $human */
        $human = $this->get('logged_user_settings')->getHuman();
        $regions = [];
        /** @var Entity\Planet\Region $settledRegion */
        foreach ($settlement->getRegions() as $settledRegion) {
            /** @var RegionRepository $repo */
            $repo = $this->getDoctrine()->getManager()->getRepository(Entity\Planet\Region::class);
            $nears = $repo->getRegionNeighbourhood($settledRegion);
            /** @var Entity\Planet\Region $near */
            foreach ($nears as $near) {
                $regions[$near->getCoords()] = $near;
            }
        }

        foreach ($settlement->getRegions() as $settledRegion) {
            unset($regions[$settledRegion->getCoords()]);
        }

        return $this->render('Settlement/connect-regions.html.twig', [
            'human' => $human,
            'settlement' => $settlement,
            'nearRegions' => $regions,
        ]);
    }

    /**
     * @Route("/{settlement}/jobs", name="settlement_jobs")
     */
    public function jobsAction(Entity\Planet\Settlement $settlement, Request $request)
    {
        /** @var Entity\Human $human */
        $human = $this->get('logged_user_settings')->getHuman();
        /** @var JobRepository $jobRepo */
        $jobRepo = $this->getDoctrine()->getManager()->getRepository(Entity\Job\ProduceJob::class);

        return $this->render('Settlement/jobs.html.twig', [
            'human' => $human,
            'settlement' => $settlement,
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
    public function connectRegionsAction(Entity\Planet\Settlement $settlement, Entity\Planet\Peak $regionC, Entity\Planet\Peak $regionL, Entity\Planet\Peak $regionR, Request $request)
    {
        /** @var Entity\Human $human */
        $human = $this->get('logged_user_settings')->getHuman();
        $region = $this->getDoctrine()->getRepository(Entity\Planet\Region::class)->findByPeaks($regionC, $regionL, $regionR);
        $region->setSettlement($settlement);
        $this->getDoctrine()->getManager()->persist($region);
        $this->getDoctrine()->getManager()->persist($settlement);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('settlement_dashboard', [
            'settlement' => $settlement->getId(),
        ]);
    }

    /**
     * @Route("/fastBuild/{project}", name="settlement_fast_build")
     */
    public function supervisedFastBuildAction(Entity\Planet\CurrentBuildingProject $project, Request $request)
    {
        /** @var Entity\Human $human */
        $human = $this->get('logged_user_settings')->getHuman();
        $settlement = $project->getRegion()->getSettlement();
        $builder = $this->get('planet_builder');
        $builder->buildProjectStep($project);
        if ($project->isDone()) {
            $builder->buildProject($project);
            $this->getDoctrine()->getManager()->remove($project);
        } else {
            $this->getDoctrine()->getManager()->persist($project);
        }
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('settlement_dashboard', [
            'settlement' => $settlement->getId(),
        ]);
    }

    /**
     * @Route("/changeType/{settlement}to{blueprint}", name="settlement_change_type")
     */
    public function changeTypeAction(Entity\Planet\Settlement $settlement, Entity\Blueprint $blueprint, Request $request)
    {
        /** @var Entity\Human $human */
        $human = $this->get('logged_user_settings')->getHuman();
        $settlement->setType($blueprint->getResourceDescriptor());
        $this->getDoctrine()->getManager()->persist($settlement);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('settlement_dashboard', [
            'settlement' => $settlement->getId(),
        ]);
    }
}
