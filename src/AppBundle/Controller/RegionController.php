<?php

namespace AppBundle\Controller;

use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="region")
 */
class RegionController extends Controller
{
	/**
	 * @Route("/build/{blueprintId}/{regionC}_{regionL}_{regionR}", name="region_build_settlement")
	 */
	public function buildAction($blueprintId, Entity\Planet\Peak $regionC, Entity\Planet\Peak $regionL, Entity\Planet\Peak $regionR, Request $request)
	{
        /** @var Entity\Human $human */
        $human = $this->get('logged_user_settings')->getHuman();
        /** @var Entity\Planet\Region $region */
		$region = $this->getDoctrine()->getRepository(Entity\Planet\Region::class)->findByPeaks($regionC, $regionL, $regionR);
		$blueprint = $this->getDoctrine()->getManager()->find(Entity\Blueprint::class, $blueprintId);

		// TODO: zkontrolovat, ze ma pravo stavet v tomto regionu

		$project = new Entity\Planet\CurrentBuildingProject();
		$project->setRegion($region);
		$project->setBuildingBlueprint($blueprint);
		$project->setMandaysLeft($blueprint->getMandays());
		$project->setMissingResources($blueprint->getRequirements());
		$project->setSupervisor($human);
		$project->setPriority(3);

		$this->getDoctrine()->getManager()->persist($project);
		$this->getDoctrine()->getManager()->persist($region);
		$this->getDoctrine()->getManager()->flush();
		return $this->redirectToRoute('settlement_dashboard', [
			'settlement' => $region->getSettlement()->getId(),
		]);
	}

    /**
     * @Route("/available-buildings/{regionC}_{regionL}_{regionR}", name="region_build_availability")
     */
    public function availableBuildingsAction(Entity\Planet\Peak $regionC, Entity\Planet\Peak $regionL, Entity\Planet\Peak $regionR, Request $request)
    {
        /** @var Entity\Human $human */
        $human = $this->get('logged_user_settings')->getHuman();
        $region = $this->getDoctrine()->getRepository(Entity\Planet\Region::class)->findByPeaks($regionC, $regionL, $regionR);
        $blueprints = $this->getDoctrine()->getManager()->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::LAND_BUILDING);

        // TODO: zkontrolovat, ze ma pravo stavet v tomto regionu

        return $this->render('Region/available-buildings-fragment.html.twig', [
            'blueprints' => $blueprints,
            'region' => $region,
            'human' => $human,
        ]);
    }

    /**
     * @Route("/available-settlements/{regionC}_{regionL}_{regionR}", name="region_settlement_availability")
     */
    public function availableSettlementsAction(Entity\Planet\Peak $regionC, Entity\Planet\Peak $regionL, Entity\Planet\Peak $regionR, Request $request)
    {
        /** @var Entity\Human $human */
        $human = $this->get('logged_user_settings')->getHuman();
        $blueprints = $this->getDoctrine()->getManager()->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::ADMINISTRATIVE_DISTRICT);
        $region = $this->getDoctrine()->getRepository(Entity\Planet\Region::class)->findByPeaks($regionC, $regionL, $regionR);

        // TODO: zkontrolovat, ze ma pravo stavet v tomto regionu

        return $this->render('Region/available-settlement-types-fragment.html.twig', [
            'blueprints' => $blueprints,
            'human' => $human,
            'region' => $region,
        ]);
    }

    /**
     * @Route("/available-types/{settlement}", name="region_settlement_availability")
     */
    public function availableSettlementTypesAction(Entity\Planet\Settlement $settlement, Request $request)
    {
        /** @var Entity\Human $human */
        $human = $this->get('logged_user_settings')->getHuman();
        $blueprints = $this->getDoctrine()->getManager()->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::ADMINISTRATIVE_DISTRICT);

        // TODO: zkontrolovat, ze ma pravo stavet v tomto regionu

        return $this->render('Region/available-settlement-types-fragment.html.twig', [
            'blueprints' => $blueprints,
            'settlement' => $settlement,
            'human' => $human,
        ]);
    }

	/**
	 * @Route("/screen/{regionUuid}", name="region_deposit_screening")
	 */
	public function depositScreeningAction($regionUuid, Request $request)
	{
	    /** @var Entity\Human $human */
        $human = $this->get('logged_user_settings')->getHuman();
		$region = $this->getDoctrine()->getRepository(Entity\Planet\Region::class)->getByUuid($regionUuid);

		srand($regionUuid);
		$deposits = [];
		if (random_int(1, 10) == 1) {
			$lightDeposit = new Entity\Planet\OreDeposit();
			$lightDeposit->setType('ironOre');
			$lightDeposit->setAmount(random_int(50, 100));
			$lightDeposit->setQuality(100);
			$lightDeposit->setPeak($region);
			$deposits[] = $lightDeposit;
			$heavyDeposit = new Entity\Planet\OreDeposit();
			$heavyDeposit->setType('ironOre');
			$heavyDeposit->setAmount(random_int(200, 1000));
			$heavyDeposit->setQuality(10);
			$heavyDeposit->setPeak($region);
			$deposits[] = $heavyDeposit;
			$region->setOreDeposits($deposits);
			$this->getDoctrine()->getManager()->persist($lightDeposit);
			$this->getDoctrine()->getManager()->persist($heavyDeposit);
		}

		if (random_int(1, 10) == 1) {
			$lightDeposit = new Entity\Planet\OreDeposit();
			$lightDeposit->setType('oil');
			$lightDeposit->setAmount(random_int(10, 100));
			$lightDeposit->setQuality(100);
			$lightDeposit->setPeak($region);
			$deposits[] = $lightDeposit;
			$heavyDeposit = new Entity\Planet\OreDeposit();
			$heavyDeposit->setType('oil');
			$heavyDeposit->setAmount(random_int(1000, 10000));
			$heavyDeposit->setQuality(10);
			$heavyDeposit->setPeak($region);
			$deposits[] = $heavyDeposit;
			$region->setOreDeposits($deposits);
			$this->getDoctrine()->getManager()->persist($lightDeposit);
			$this->getDoctrine()->getManager()->persist($heavyDeposit);
		}

		$this->getDoctrine()->getManager()->persist($region);
		$this->getDoctrine()->getManager()->flush();

		return $this->redirectToRoute('human_dashboard', [
			'human' => $human->getId(),
		]);
	}
}
