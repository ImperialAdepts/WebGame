<?php

namespace PlanetBundle\Controller;

use AppBundle\Builder\PlanetBuilder;
use AppBundle\Entity\Human;
use AppBundle\Entity\Human\Event;
use AppBundle\Entity\Human\EventDataTypeEnum;
use AppBundle\Entity\Human\EventTypeEnum;
use AppBundle\Entity\Human\SettlementTitle;
use AppBundle\Entity\SolarSystem\Planet;
use AppBundle\PlanetConnection\DynamicPlanetConnector;
use PlanetBundle\Builder\BlueprintRecipe\ResourceDescriptorBuilder;
use PlanetBundle\Concept\Food;
use PlanetBundle\Concept\House;
use PlanetBundle\Concept\People;
use PlanetBundle\Entity;
use AppBundle\Repository\JobRepository;
use PlanetBundle\Entity\Peak;
use PlanetBundle\Entity\Region;
use PlanetBundle\Form\BuildersFormType;
use PlanetBundle\Form\PeakSelectorType;
use PlanetBundle\Form\RegionSelectorType;
use PlanetBundle\Repository\RegionRepository;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use PlanetBundle\UseCase;
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
            if ($region->getDeposit() != null) {
                $blueprintsByRegions[$region->getCoords()] = $builder->getAvailableBlueprints($region->getDeposit(), $settlement->getManager());
            }
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
        $warehouses = $settlement->getDeposit()->filterByUseCase(UseCase\Deposit::class);
        $portables = $settlement->getDeposit()->filterByUseCase(UseCase\Portable::class);

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
        $warehouse = new Entity\Resource\Thing($blueprint, 1);
        $warehouse->setDescription('warehouse');
        $warehouse->setUseCases([
            UseCase\LandBuilding::class,
        ]);
        $settlement->getDeposit()->addResourceDescriptors($warehouse);

        $this->getDoctrine()->getManager('planet')->persist($warehouse);
        $this->getDoctrine()->getManager('planet')->persist($settlement);
        $this->getDoctrine()->getManager('planet')->flush();

        $buildings = $settlement->getDeposit()->filterByUseCase(UseCase\LandBuilding::class);

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
        $houses = $settlement->getDeposit()->filterByConcept(House::class);
        $peopleBirths = 0;
        $peoples = $settlement->getDeposit()->filterByConcept(People::class);
        foreach ($this->get('maintainer_population')->getBirths($settlement->getDeposit()) as $birthCount) {
            $peopleBirths += $birthCount;
        }

        $foods = $settlement->getDeposit()->filterByUseCase(UseCase\Consumable::class);
        $foodEnergy = Entity\Deposit::sumCallbacks($foods, function ($food) { return $food->getEnergy(); });
        $housingCapacity = Entity\Deposit::sumCallbacks($houses, function (House $house) { return $house->getPeopleCapacity(); });
        $consumation = Entity\Deposit::sumCallbacks($peoples, function (People $people) { return $people->getBasalMetabolism(DynamicPlanetConnector::getPlanet()); });

        $timeLeft = Entity\Deposit::sumCallbacks($foods, function (Food $food) use ($settlement) { return $food->getTimeDeposit($settlement); });

        return $this->render('Settlement/housing.html.twig', [
            'settlement' => $settlement,
            'people' => Entity\Deposit::sumAmounts($peoples),
            'peopleBirths' => $peopleBirths,
            'foods' => $foods,
            'foodEnergy' => $foodEnergy,
            'foodVariety' => Entity\Deposit::countVariety($foods),
            'foodEnergyConsumation' => $consumation,
            'foodTimeElapsed' => $timeLeft,
            'housingCapacity' => $housingCapacity,
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
        $transporterBlueprints = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Resource\Blueprint::class)->getByUseCase(UseCase\TeamTransporters::class);
        $builderBlueprints = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Resource\Blueprint::class)->getByUseCase(UseCase\TeamBuilders::class);
        $merchantBlueprints = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Resource\Blueprint::class)->getByUseCase(UseCase\TeamMerchants::class);
        $scientistBlueprints = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Resource\Blueprint::class)->getByUseCase(UseCase\TeamScientists::class);
        $workerBlueprints = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Resource\Blueprint::class)->getByUseCase(UseCase\TeamWorkers::class);
        $farmerBlueprints = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Resource\Blueprint::class)->getByUseCase(UseCase\TeamFarmers::class);
        $armyBlueprints = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Resource\Blueprint::class)->getByUseCase(UseCase\TeamSoldiers::class);

        /** @var Adapters\LivingBuilding[] $teams */
        $teams = $region->getDeposit() ? $region->getDeposit()->filterByUseCase(UseCase\Team::class) : [];
        $peopleCount = $region->getPeopleCount();
        $employees = 0;

        foreach ($teams as $team) {
            $employees += $team->getConceptAdapter()->getPeopleCount();
        }

        return $this->render('Settlement/teams.html.twig', [
            'settlement' => $region->getSettlement(),
            'region' => $region,
            'people' => $peopleCount,
            'unemployedPeople' => $peopleCount - $employees,
            'transporters' => $region->getDeposit() ? $region->getDeposit()->filterByUseCase(UseCase\TeamTransporters::class) : [],
            'builders' => $region->getDeposit() ? $region->getDeposit()->filterByUseCase(UseCase\TeamBuilders::class) : [],
            'merchants' => $region->getDeposit() ? $region->getDeposit()->filterByUseCase(UseCase\TeamMerchants::class) : [],
            'scientists' => $region->getDeposit() ? $region->getDeposit()->filterByUseCase(UseCase\TeamScientists::class) : [],
            'workers' => $region->getDeposit() ? $region->getDeposit()->filterByUseCase(UseCase\TeamWorkers::class) : [],
            'farmers' => $region->getDeposit() ? $region->getDeposit()->filterByUseCase(UseCase\TeamFarmers::class) : [],
            'army' => $region->getDeposit() ? $region->getDeposit()->filterByUseCase(UseCase\TeamSoldiers::class) : [],
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
    public function createTeamAction(Entity\Peak $peakC, Entity\Peak $peakL, Entity\Peak $peakR, Entity\Resource\Blueprint $blueprint, Request $request)
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
    public function changeTypeAction(Entity\Settlement $settlement, Entity\Resource\Blueprint $blueprint, Request $request)
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
     * popup
     * @Route("/available-buildings", name="settlement_build_availability")
     */
    public function availableBuildingsAction(Planet $planet, Entity\Settlement $settlement, Request $request)
    {
        $recipes = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Resource\BlueprintRecipe::class)->findAll();

        return $this->render('Region/available-buildings-fragment.html.twig', [
            'builderForm' => $this->createForm(BuildersFormType::class, [], [
                'blueprints' => $recipes,
                'action' => $this->generateUrl('settlement_buildform_handler', [
                    'planet' => $this->planet->getId(),
                    'settlement' => $settlement->getId(),
                ]),
            ])->createView(),
            'settlement' => $settlement,
            'human' => $this->getHuman(),
        ]);
    }

    /**
     * @Route("/builder-form-handler", name="settlement_buildform_handler")
     */
    public function handleBuilderFormAction(Planet $planet, Entity\Settlement $settlement, Request $request)
    {
        $recipe = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Resource\BlueprintRecipe::class)->findAll();

        $built = 0;
        foreach ($request->get('builders_form') as $recipeId => $options) {
            if ($settlement->getDeposit() != null && isset($options['count']) && ($count = $options['count']) > 0) {
                $recipe = $this->getDoctrine()->getManager('planet')->find(Entity\Resource\BlueprintRecipe::class, $recipeId);

                // TODO: zkontrolovat, ze ma pravo stavet v tomto regionu
                $this->getDoctrine()->getManager('planet')->transactional(function ($em) use ($recipe, $settlement, $count, &$built) {
                    $builder = new ResourceDescriptorBuilder($settlement->getDeposit(), $recipe);
                    $builder->setSupervisor($this->getHuman());
                    $builder->setAllRegionTeams();
                    $builder->setCount($count);
                    $built += $builder->build();
                });

                $this->createEvent(EventTypeEnum::SETTLEMENT_BUILD, [
                    EventDataTypeEnum::BLUEPRINT => [
                        'id' => $recipe->getId(),
                        'desc' => $recipe->getDescription(),
                    ],
                    EventDataTypeEnum::REGION => $settlement->getName(),
                    'countRequested' => $options['count'],
                    'countBuilt' => $built,
                ]);
            }
        }

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
