<?php

namespace AppBundle\Controller;

use AppBundle\Descriptor\Adapters;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Entity;
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
	 * @Route("/dashboard/{settlement}", name="settlement_dashboard")
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
        ]);
	}

    /**
     * @Route("/settlement_warehouses/{settlement}/{human}", name="settlement_warehouses")
     */
    public function warehouseContentAction(Entity\Planet\Settlement $settlement, Entity\Human $human, Request $request)
    {
        $blueprints = $this->getDoctrine()->getManager()->getRepository(Entity\Blueprint::class)->getWarehouseable();
        $resourceDescriptors = [];
        /** @var Entity\Blueprint $blueprint */
        foreach ($blueprints as $blueprint) {
            $resourceDescriptors[$blueprint->getResourceDescriptor()] = null;
        }
        $warehouseContent = [];
        foreach ($settlement->getResourceDeposits() as $deposit) {
            if (array_key_exists($deposit->getResourceDescriptor(), $resourceDescriptors)) {
                $warehouseContent[] = $deposit;
            }
        }

        return $this->render('Settlement/warehouse-content-fragment.html.twig', [
            'resources' => $warehouseContent,
            'human' => $human,
        ]);
    }

    /**
     * @Route("/settlement_buildings/{settlement}", name="settlement_buildings")
     */
    public function buildingsAction(Entity\Planet\Settlement $settlement, Request $request)
    {
        /** @var Entity\Human $human */
        $human = $this->get('logged_user_settings')->getHuman();
        $blueprints = $this->getDoctrine()->getManager()->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::LAND_BUILDING);
        $resourceDescriptors = [];
        /** @var Entity\Blueprint $blueprint */
        foreach ($blueprints as $blueprint) {
            $resourceDescriptors[$blueprint->getResourceDescriptor()] = null;
        }
        $buldings = [];
        foreach ($settlement->getResourceDeposits() as $deposit) {
            if (array_key_exists($deposit->getResourceDescriptor(), $resourceDescriptors)) {
                $buldings[] = $deposit;
            }
        }

        return $this->render('Settlement/buildings-fragment.html.twig', [
            'buildings' => $buldings,
            'human' => $human,
        ]);
    }

    /**
     * @Route("/settlement_housing/{settlement}", name="settlement_housing")
     */
    public function housingAction(Entity\Planet\Settlement $settlement, Request $request)
    {
        /** @var Entity\Human $human */
        $human = $this->get('logged_user_settings')->getHuman();
        $blueprints = $this->getDoctrine()->getManager()->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::LIVING_BUILDINGS);
        $resourceDescriptors = [];
        /** @var Entity\Blueprint $blueprint */
        foreach ($blueprints as $blueprint) {
            $resourceDescriptors[$blueprint->getResourceDescriptor()] = null;
        }

        /** @var Adapters\LivingBuilding[] $houses */
        $houses = Adapters\LivingBuilding::in($settlement);
        $peopleCount = $settlement->getPeopleCount();

        $foodEnergy = 0;
        foreach ($settlement->getResourceDeposits(ResourceDescriptorEnum::SIMPLE_FOOD) as $deposit) {
            $foodEnergy += $deposit->getAmount();
        }

        return $this->render('Settlement/housing.html.twig', [
            'people' => $peopleCount,
            'foodEnergy' => $foodEnergy,
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

        /** @var Adapters\LivingBuilding[] $teams */
        $teams = Adapters\Team::in($region);
        $peopleCount = Adapters\Team::countPeople($teams);

        return $this->render('Settlement/teams.html.twig', [
            'region' => $region,
            'people' => $peopleCount,
            'unemployedPeople' => $peopleCount/2,
            'transporters' => Adapters\TeamTransporter::in($region),
            'builders' => Adapters\TeamBuilder::in($region),
            'merchants' => Adapters\TeamMerchant::in($region),
            'scientists' => Adapters\TeamScientist::in($region),
            'workers' => Adapters\TeamWorker::in($region),
            'farmers' => Adapters\TeamFarmer::in($region),
            'transporterBlueprints' => $transporterBlueprints,
            'builderBlueprints' => $builderBlueprints,
            'merchantBlueprints' => $merchantBlueprints,
            'scientistBlueprints' => $scientistBlueprints,
            'workerBlueprints' => $workerBlueprints,
            'farmerBlueprints' => $farmerBlueprints,
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
        return $this->redirectToRoute('settlement_dashboard', [
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
