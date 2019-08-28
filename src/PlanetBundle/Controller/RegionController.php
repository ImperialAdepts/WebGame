<?php

namespace PlanetBundle\Controller;

use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Entity\Human\EventDataTypeEnum;
use AppBundle\Entity\Human\EventTypeEnum;
use PlanetBundle\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="region")
 */
class RegionController extends BasePlanetController
{
	/**
	 * @Route("/build-plan/{blueprint}/{regionC}_{regionL}_{regionR}", name="region_build_plan_settlement")
	 */
	public function buildPlanAction(Entity\Blueprint $blueprint, Entity\Peak $regionC, Entity\Peak $regionL, Entity\Peak $regionR, Request $request)
	{
        /** @var Entity\Region $region */
		$region = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Region::class)->findByPeaks($regionC, $regionL, $regionR);

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
			'settlement' => $region->getSettlement()->getId(),
		]);
	}

    /**
     * @Route("/build/{blueprint}/{regionC}_{regionL}_{regionR}/{count}", name="region_build_settlement")
     */
    public function buildAction(Entity\Blueprint $blueprint, Entity\Peak $regionC, Entity\Peak $regionL, Entity\Peak $regionR, $count = 1, Request $request)
    {
        /** @var Entity\Region $region */
        $region = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Region::class)->findByPeaks($regionC, $regionL, $regionR);

        // TODO: zkontrolovat, ze ma pravo stavet v tomto regionu
        $this->getDoctrine()->getManager('planet')->transactional(function ($em) use ($blueprint, $region, $count) {
            $builder = $this->get('builder_factory')->createRegionBuilder($blueprint);
            $builder->setResourceHolder($region);
            $builder->setSupervisor($this->getHuman());
            $builder->setAllRegionTeams();
            $builder->setCount($count);
            $builder->build();
        });

        $this->createEvent(EventTypeEnum::SETTLEMENT_BUILD, [
            EventDataTypeEnum::BLUEPRINT => $blueprint,
            EventDataTypeEnum::REGION => $region,
        ]);

        return $this->redirectToRoute('settlement_buildings', [
            'settlement' => $region->getSettlement()->getId(),
        ]);
    }

    /**
     * popup
     * @Route("/available-buildings/{regionC}_{regionL}_{regionR}", name="region_build_availability")
     */
    public function availableBuildingsAction(Entity\Peak $regionC, Entity\Peak $regionL, Entity\Peak $regionR, Request $request)
    {
        $region = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Region::class)->findByPeaks($regionC, $regionL, $regionR);
        $blueprints = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::LAND_BUILDING);

        $blueprintAnalyzes = [];
        foreach ($blueprints as $blueprint) {
            $builder = $this->get('builder_factory')->createRegionBuilder($blueprint);
            $builder->setResourceHolder($region);
            $builder->setSupervisor($this->getHuman());
            $builder->setAllRegionTeams();
            $blueprintAnalyzes[$blueprint->getId()]['valid'] = $builder->isValidBuildable();
            $blueprintAnalyzes[$blueprint->getId()]['count'] = $builder->getPosibilityCount();
            $blueprintAnalyzes[$blueprint->getId()]['validationErrors'] = [];
            if (!$builder->isValidBuildable()) {
                $blueprintAnalyzes[$blueprint->getId()]['validationErrors'] = $builder->getValidationErrors();
            }
        }

        return $this->render('Region/available-buildings-fragment.html.twig', [
            'blueprints' => $blueprints,
            'blueprintAnalyzes' => $blueprintAnalyzes,
            'region' => $region,
            'human' => $this->getHuman(),
        ]);
    }

    /**
     * @Route("/available-settlements/{regionC}_{regionL}_{regionR}", name="region_settlement_availability")
     */
    public function availableSettlementsAction(Entity\Peak $regionC, Entity\Peak $regionL, Entity\Peak $regionR, Request $request)
    {
        $blueprints = $this->getDoctrine()->getManager('planet')->getManager()->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::ADMINISTRATIVE_DISTRICT);
        $region = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Region::class)->findByPeaks($regionC, $regionL, $regionR);

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
        $blueprints = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::ADMINISTRATIVE_DISTRICT);

        // TODO: zkontrolovat, ze ma pravo stavet v tomto regionu

        return $this->render('Region/available-settlement-types-fragment.html.twig', [
            'blueprints' => $blueprints,
            'settlement' => $settlement,
            'human' => $this->getHuman(),
        ]);
    }

	/**
	 * @Route("/screen/{regionUuid}", name="region_deposit_screening")
	 */
	public function depositScreeningAction($regionUuid, Request $request)
	{
	    /** @var Entity\Region $region */
		$region = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Region::class)->getByUuid($regionUuid);

		srand($regionUuid);
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
