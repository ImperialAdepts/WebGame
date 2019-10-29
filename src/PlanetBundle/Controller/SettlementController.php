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
use AppBundle\Entity\SolarSystem\Planet;
use AppBundle\PlanetConnection\DynamicPlanetConnector;
use PlanetBundle\Concept\Food;
use PlanetBundle\Concept\People;
use PlanetBundle\Entity;
use AppBundle\Repository\JobRepository;
use PlanetBundle\Entity\Peak;
use PlanetBundle\Entity\Region;
use PlanetBundle\Form\PeakSelectorType;
use PlanetBundle\Form\RegionSelectorType;
use PlanetBundle\Repository\RegionRepository;
use PlanetBundle\UseCase\Deposit;
use PlanetBundle\UseCase\LandBuilding;
use PlanetBundle\UseCase\LivingBuilding;
use PlanetBundle\UseCase\Portable;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Tracy\Debugger;

/**
 * @Route(path="planet-{planet}/settlement-{settlement}")
 */
class SettlementController extends BasePlanetController
{
    public function init()
    {
        parent::init();

        $settlementId = $this->get('request_stack')->getCurrentRequest()->get('settlement');
        if ($settlementId != null) {
            $settlement = $this->get('repo_settlement')->find($settlementId);
        } else {
            $settlement = $this->human->getCurrentPeakPosition()->getSettlement();
        }

        if ($settlement === null) {
            throw new NotFoundHttpException("There is no such settlement with id " . $settlementId);
        }

        $this->get('twig')->addGlobal('currentSettlement', $settlement);
        $this->get('twig')->addGlobal('currentSettlementManager', $this->getDoctrine()->getManager()
                ->getRepository(Human::class)->find($settlement->getManager()->getGlobalHumanId()));
        $this->get('twig')->addGlobal('currentSettlementOwner', $this->getDoctrine()->getManager()
            ->getRepository(Human::class)->find($settlement->getOwner()->getGlobalHumanId()));
    }


    /**
	 * @Route("/dashboard", name="settlement_dashboard")
	 */
	public function dashboardAction(Planet $planet, Entity\Settlement $settlement, Request $request)
	{
        /** @var PlanetBuilder $builder */
        $builder = $this->get('planet_builder');
        $blueprintsByRegions = [];
        /** @var Entity\Region $region */
        foreach ($settlement->getRegions() as $region) {
            // TODO: zmenit na aktualne prihlaseneho cloveka
            $blueprintsByRegions[$region->getCoords()] = $builder->getAvailableBlueprints($region, $settlement->getManager());
        }

        $peaks = [];
        foreach ($settlement->getRegions() as $region) {
            $peaks[$region->getPeakLeft()->getId()] = $region->getPeakLeft();
            $peaks[$region->getPeakCenter()->getId()] = $region->getPeakCenter();
            $peaks[$region->getPeakRight()->getId()] = $region->getPeakRight();
        }
        foreach ($settlement->getPeaks() as $peak) {
            unset($peaks[$peak->getId()]);
        }

        $cutOffForm = $this->createFormBuilder(null, [
            'action' => $this->generateUrl('settlement_cut', [
                'planet' => $planet->getId(),
                'settlement' => $settlement->getId(),
            ]),
        ])
            ->add('regionsToMigrate', ChoiceType::class, [
                'multiple' => true,
                'choices' => $settlement->getRegions(),
                'choice_label' => function (Region $region) {
                    return $region->getName();
                },
                'choice_value' => function (Region $region) {
                    return $region->getCoords();
                },
                'required' => false,
            ])
            ->add('newAdministrativeCenter', ChoiceType::class, [
                'multiple' => false,
                'choices' => $peaks,
                'choices_as_values' => true,
                'choice_label' => function (Peak $peak) {
                    return $peak->getId();
                },
                'required' => true,
            ])
            ->add('cutoff', SubmitType::class)
            ;


        return $this->render('Settlement/dashboard.html.twig', [
            'settlement' => $settlement,
            'buildingBlueprints' => $blueprintsByRegions,
            'human' => $settlement->getManager(),
            'cutOffForm' => $cutOffForm->getForm()->createView(),
            'foodConsumption' => $this->get('maintainer_food')->getFoodConsumptionEstimation($settlement->getDeposit()),
            'populationChanges' => $this->get('maintainer_population')->getBirths($settlement->getDeposit()),
        ]);
	}

    /**
     * @Route("/warehouses", name="settlement_warehouses")
     */
    public function warehouseContentAction(Entity\Settlement $settlement, Request $request)
    {
        $warehouses = $settlement->getDeposit()->filterByUseCase(Deposit::class);
        $portables = $settlement->getDeposit()->filterByUseCase(Portable::class);

        return $this->render('Settlement/warehouse-content-fragment.html.twig', [
            'settlement' => $settlement,
            'resources' => $portables,
            'warehouses' => $warehouses,
            'human' => $this->getHuman(),
        ]);
    }

    /**
     * @Route("/buildings", name="settlement_buildings")
     */
    public function buildingsAction(Entity\Settlement $settlement, Request $request)
    {
        if ($settlement->getAdministrativeCenter()->getDeposit() == null) {
            $deposit = new Entity\PeakDeposit();
            $deposit->setPeak($settlement->getAdministrativeCenter());
            $this->getDoctrine()->getManager('planet')->persist($deposit);
            $this->getDoctrine()->getManager('planet')->persist($settlement);
        }

        $blueprint = $this->get('repo_blueprint')->find(1);
        $warehouse = new Entity\Resource\Thing();
        $warehouse->setDescription('warehouse');
        $warehouse->setAmount(1);
        $warehouse->setUseCases([
            LandBuilding::class,
        ]);
        $warehouse->setBlueprint($blueprint);
        $settlement->getDeposit()->addResourceDescriptors($warehouse);

        $this->getDoctrine()->getManager('planet')->persist($warehouse);
        $this->getDoctrine()->getManager('planet')->persist($settlement);
        $this->getDoctrine()->getManager('planet')->flush();

        $buildings = $settlement->getDeposit()->filterByUseCase(LandBuilding::class);

        return $this->render('Settlement/buildings-fragment.html.twig', [
            'settlement' => $settlement,
            'buildings' => $buildings,
            'human' => $this->getHuman(),
        ]);
    }

    /**
     * @Route("/housing", name="settlement_housing")
     */
    public function housingAction(Entity\Settlement $settlement, Request $request)
    {
        $houses = $settlement->getDeposit()->filterByUseCase(LivingBuilding::class);
        $peopleCount = 0;
        $peopleBirths = 0;
        foreach ($settlement->getDeposit()->filterByConcept(People::class) as $people) {
            $peopleCount += $people->getAmount();
        }
        foreach ($this->get('maintainer_population')->getBirths($settlement->getDeposit()) as $birthCount) {
            $peopleBirths += $birthCount;
        }

        $foods = $settlement->getDeposit()->filterByConcept(Food::class);

        return $this->render('Settlement/housing.html.twig', [
            'settlement' => $settlement,
            'people' => $peopleCount,
            'peopleBirths' => $peopleBirths,
            'foods' => $foods,
            'foodEnergy' => 0,//Adapters\BasicFood::countEnergy($foods),
            'foodVariety' => Adapters\BasicFood::countVariety($this->get('maintainer_food')->getFoodConsumptionEstimation($settlement->getDeposit())),
            'housingCapacity' => Adapters\LivingBuilding::countLivingCapacity($houses),
            'houses' => $houses,
            'human' => $this->getHuman(),
        ]);
    }

    /**
     * @Route("/teams/{peakC}_{peakL}_{peakR}", name="settlement_teams")
     */
    public function teamsAction(Entity\Peak $peakC, Entity\Peak $peakL, Entity\Peak $peakR, Request $request)
    {
        /** @var Entity\Region $region */
        $region = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Region::class)->findByPeaks($peakC, $peakL, $peakR);
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
     * @Route("/create-team/{peakC}_{peakL}_{peakR}/{blueprint}", name="settlement_team_create")
     */
    public function createTeamAction(Entity\Peak $peakC, Entity\Peak $peakL, Entity\Peak $peakR, Entity\Blueprint $blueprint, Request $request)
    {
        /** @var Entity\Region $region */
        $region = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Region::class)->findByPeaks($peakC, $peakL, $peakR);
        $region->addResourceDeposit($blueprint, 1);
        $this->getDoctrine()->getManager('planet')->persist($region);
        $this->getDoctrine()->getManager('planet')->flush();
        return $this->redirectToRoute('settlement_jobs', [
            'settlement' => $region->getSettlement()->getId(),
        ]);
    }

    /**
     * @Route("/connectableRegions", name="settlement_connectable_regions")
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
     * @Route("/jobs", name="settlement_jobs")
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
     * @Route("/connectRegions/{peakC}_{peakL}_{peakR}", name="settlement_connect_regions")
     */
    public function connectRegionsAction(Entity\Settlement $settlement, Entity\Peak $peakC, Entity\Peak $peakL, Entity\Peak $peakR, Request $request)
    {
        $region = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Region::class)->findByPeaks($peakC, $peakL, $peakR);
        $region->setSettlement($settlement);
        $this->getDoctrine()->getManager('planet')->persist($region);
        $this->getDoctrine()->getManager('planet')->persist($settlement);
        $this->getDoctrine()->getManager('planet')->flush();

        $this->createEvent(EventTypeEnum::SETTLEMENT_EXPAND, [
            EventDataTypeEnum::REGION => $region,
        ]);

        return $this->redirectToRoute('settlement_dashboard', [
            'planet' => $this->planet->getId(),
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
            'planet' => $this->planet->getId(),
            'settlement' => $settlement->getId(),
        ]);
    }

    /**
     * @Route("/changeType/to{blueprint}", name="settlement_change_type")
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
            'planet' => $this->planet->getId(),
            'settlement' => $settlement->getId(),
        ]);
    }

    /**
     * @Route("/cut-to-half", name="settlement_cut")
     */
    public function cutOutHalfAction(Entity\Settlement $settlement, Request $request) {
        $remainsRegions = [];
        $transferRegions = [];
        $count = count($settlement->getRegions());
        $formData = $request->get('form');
        $regionCoords = $formData['regionsToMigrate'];

        foreach ($settlement->getRegions() as $region) {
            if (in_array($region->getCoords(), $regionCoords)) {
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

            'planet' => $this->planet->getId(),
            'settlement' => $settlement->getId(),
        ]);
    }

    /**
     * @Route("/events", name="settlement_events")
     */
    public function actionEventList(Planet $planet, Entity\Settlement $settlement) {
        $events = $this->getDoctrine()->getManager()
            ->getRepository(Event::class)->findBy([
                'planet' => $planet->getId(),
//                'settlement' => $settlement->getId(),
            ]);

        return $this->render('Event/list-fragment.html.twig', [
            'events' => $events,
        ]);
    }
}
