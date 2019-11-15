<?php

namespace PlanetBundle\Controller;

use PlanetBundle\Builder\RegionBuilder;
use PlanetBundle\UseCase;
use AppBundle\Entity\Human\EventDataTypeEnum;
use AppBundle\Entity\Human\EventTypeEnum;
use AppBundle\Entity\SolarSystem\Planet;
use PlanetBundle\Entity;
use PlanetBundle\Form\BuildersFormType;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Tracy\Debugger;

/**
 * @Route(path="planet-{planet}/region-{peakC}_{peakL}_{peakR}")
 */
class RegionController extends BasePlanetController
{
	/**
	 * @Route("/build-plan/{blueprint}", name="region_build_plan_settlement")
	 */
	public function buildPlanAction(Entity\Resource\Blueprint $blueprint, Entity\Peak $peakC, Entity\Peak $peakL, Entity\Peak $peakR, Request $request)
	{
        /** @var Entity\Region $region */
		$region = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Region::class)->findByPeaks($peakC, $peakL, $peakR);

		// TODO: zkontrolovat, ze ma pravo stavet v tomto regionu

		$project = new Entity\CurrentBuildingProject();
		$project->setRegion($region);
		$project->setBuildingBlueprint($blueprint);
		$project->setMandaysLeft($blueprint->getMandays());
		$project->setMissingResources($blueprint->getResourceRequirements());
		$project->setSupervisor($this->getHuman());
		$project->setPriority(3);

		$this->getDoctrine()->getManager('planet')->persist($project);
		$this->getDoctrine()->getManager('planet')->persist($region);
		$this->getDoctrine()->getManager('planet')->flush();

        $this->createEvent(EventTypeEnum::SETTLEMENT_BUILD_PLAN, [
            EventDataTypeEnum::BLUEPRINT => $blueprint,
            EventDataTypeEnum::REGION => $region,
        ]);

		return $this->redirectToRoute('settlement_dashboard', [
            'planet' => $this->planet->getId(),
			'settlement' => $region->getSettlement()->getId(),
		]);
	}

    /**
     * @Route("/build/{recipe}/{count}", name="region_build_settlement")
     */
    public function buildAction(Entity\Resource\BlueprintRecipe $recipe, Entity\Peak $peakC, Entity\Peak $peakL, Entity\Peak $peakR, $count = 1, Request $request)
    {
        /** @var Entity\Region $region */
        $region = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Region::class)->findByPeaks($peakC, $peakL, $peakR);

        // TODO: zkontrolovat, ze ma pravo stavet v tomto regionu
        $this->getDoctrine()->getManager('planet')->transactional(function ($em) use ($recipe, $region, $count) {
            $builder = new RegionBuilder($region->getDeposit(), $recipe);
            $builder->setSupervisor($this->getHuman());
            $builder->setAllRegionTeams();
            $builder->setCount($count);
            $builder->build();
        });

        $this->createEvent(EventTypeEnum::SETTLEMENT_BUILD, [
            EventDataTypeEnum::BLUEPRINT => $recipe,
            EventDataTypeEnum::REGION => $region,
        ]);

        return $this->redirectToRoute('settlement_buildings', [
            'settlement' => $region->getSettlement()->getId(),
        ]);
    }

    /**
     * popup
     * @Route("/available-buildings", name="region_build_availability")
     */
    public function availableBuildingsAction(Entity\Peak $peakC, Entity\Peak $peakL, Entity\Peak $peakR, Request $request)
    {
        $region = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Region::class)->findByPeaks($peakC, $peakL, $peakR);
        $recipes = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Resource\BlueprintRecipe::class)->findAll();

        return $this->render('Region/available-buildings-fragment.html.twig', [
            'builderForm' => $this->createForm(BuildersFormType::class, [], [
                'blueprints' => $recipes,
                'action' => $this->generateUrl('region_buildform_handler', [
                    'planet' => $this->planet->getId(),
                    'peakC' => $peakC->getId(),
                    'peakL' => $peakL->getId(),
                    'peakR' => $peakR->getId(),
                ]),
            ])->createView(),
            'region' => $region,
            'human' => $this->getHuman(),
        ]);
    }

    /**
     * @Route("/builder-form-handler", name="region_buildform_handler")
     */
    public function handleBuilderFormAction(Planet $planet, Entity\Peak $peakC, Entity\Peak $peakL, Entity\Peak $peakR, Request $request)
    {
        /** @var Entity\Region $region */
        $region = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Region::class)->findByPeaks($peakC, $peakL, $peakR);
        $blueprints = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Resource\Blueprint::class)->getByUseCase(UseCase\LandBuilding::class);

        $form = $this->createForm(BuildersFormType::class, $request->get('builders_form'), [
            'blueprints' => $blueprints,
            'action' => $this->generateUrl('region_buildform_handler', [
                'planet' => $planet->getId(),
                'peakC' => $peakC->getId(),
                'peakL' => $peakL->getId(),
                'peakR' => $peakR->getId(),
            ]),
        ]);


        $built = 0;
        foreach ($request->get('builders_form') as $recipeId => $options) {
            if ($region->getDeposit() == null) {
                $region->setDeposit(new Entity\RegionDeposit());
            }


            if ($region->getDeposit() != null && isset($options['count']) && ($count = $options['count']) > 0) {
                $recipe = $this->getDoctrine()->getManager('planet')->find(Entity\Resource\BlueprintRecipe::class, $recipeId);

                $built = 0;
                // TODO: zkontrolovat, ze ma pravo stavet v tomto regionu
                $this->getDoctrine()->getManager('planet')->transactional(function ($em) use ($recipe, $region, $count, &$built) {
                    $builder = new RegionBuilder($region->getDeposit(), $recipe);
                    $builder->setSupervisor($this->getHuman());
                    $builder->setAllRegionTeams();
                    $builder->setCount($count);
                    $built = $builder->build();
                });

                $this->createEvent(EventTypeEnum::SETTLEMENT_BUILD, [
                    EventDataTypeEnum::BLUEPRINT => [
                        'id' => $recipe->getId(),
                        'desc' => $recipe->getDescription(),
                    ],
                    EventDataTypeEnum::REGION => $region->getName(),
                    'countRequested' => $options['count'],
                    'countBuilt' => $built,
                ]);
            }
        }

        return $this->redirectToRoute('settlement_dashboard', [
            'planet' => $this->planet->getId(),
            'settlement' => $region->getSettlement()->getId(),
        ]);
    }

    /**
     * @Route("/available-settlements", name="region_settlement_availability")
     */
    public function availableSettlementsAction(Entity\Peak $peakC, Entity\Peak $peakL, Entity\Peak $peakR, Request $request)
    {
        $blueprints = $this->getDoctrine()->getManager('planet')->getManager()->getRepository(Entity\Resource\Blueprint::class)->getByUseCase(UseCase\AdministrativeDistrict::class);
        $region = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Region::class)->findByPeaks($peakC, $peakL, $peakR);

        // TODO: zkontrolovat, ze ma pravo stavet v tomto regionu

        return $this->render('Region/available-settlement-types-fragment.html.twig', [
            'blueprints' => $blueprints,
            'human' => $this->getHuman(),
            'region' => $region,
        ]);
    }

    /**
     * @Route("/available-types/{settlement}", name="region_settlement_availability")
     */
    public function availableSettlementTypesAction(Entity\Settlement $settlement, Request $request)
    {
        $blueprints = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Resource\Blueprint::class)->getByUseCase(UseCase\AdministrativeDistrict::class);

        // TODO: zkontrolovat, ze ma pravo stavet v tomto regionu

        return $this->render('Region/available-settlement-types-fragment.html.twig', [
            'blueprints' => $blueprints,
            'settlement' => $settlement,
            'human' => $this->getHuman(),
        ]);
    }

	/**
	 * @Route("/screen", name="region_deposit_screening")
	 */
	public function depositScreeningAction(Entity\Peak $peakC, Entity\Peak $peakL, Entity\Peak $peakR, Request $request)
	{
	    /** @var Entity\Region $region */
        $region = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Region::class)->findByPeaks($peakC, $peakL, $peakR);

		$deposits = [];
		if (random_int(1, 10) == 1) {
			$lightDeposit = new Entity\OreDeposit();
			$lightDeposit->setType('ironOre');
			$lightDeposit->setAmount(random_int(50, 100));
			$lightDeposit->setQuality(100);
			$lightDeposit->setPeak($region);
			$deposits[] = $lightDeposit;
			$heavyDeposit = new Entity\OreDeposit();
			$heavyDeposit->setType('ironOre');
			$heavyDeposit->setAmount(random_int(200, 1000));
			$heavyDeposit->setQuality(10);
			$heavyDeposit->setPeak($region);
			$deposits[] = $heavyDeposit;
			$this->getDoctrine()->getManager('planet')->persist($lightDeposit);
			$this->getDoctrine()->getManager('planet')->persist($heavyDeposit);
		}

		if (random_int(1, 10) == 1) {
			$lightDeposit = new Entity\OreDeposit();
			$lightDeposit->setType('oil');
			$lightDeposit->setAmount(random_int(10, 100));
			$lightDeposit->setQuality(100);
			$lightDeposit->setPeak($region);
			$deposits[] = $lightDeposit;
			$heavyDeposit = new Entity\OreDeposit();
			$heavyDeposit->setType('oil');
			$heavyDeposit->setAmount(random_int(1000, 10000));
			$heavyDeposit->setQuality(10);
			$heavyDeposit->setPeak($region);
			$deposits[] = $heavyDeposit;
			$this->getDoctrine()->getManager('planet')->persist($lightDeposit);
			$this->getDoctrine()->getManager('planet')->persist($heavyDeposit);
		}
        $region->getPeakCenter()->setOreDeposits($deposits);

		$this->getDoctrine()->getManager('planet')->persist($region);
		$this->getDoctrine()->getManager('planet')->flush();

        $this->createEvent(EventTypeEnum::RESOURCES_SCREENING, [
            EventDataTypeEnum::REGION => $region,
            EventDataTypeEnum::NEW_RESOURCES => $deposits,
        ]);

		return $this->redirectToRoute('human_dashboard', [
			'human' => $this->getHuman(),
		]);
	}
}
