<?php

namespace AppBundle\Controller;

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
	 * @Route("/build/{type}/{regionUuid}/{human}", name="region_build_settlement")
	 */
	public function buildAction($type, $regionUuid, Entity\Human $human, Request $request)
	{
		$region = $this->getDoctrine()->getRepository(Entity\Planet\Region::class)->getByUuid($regionUuid);

		$project = new Entity\Planet\BuildingProject();
		$project->setRegion($region);
		$project->setBuilding($type);
		// TODO: vytahnout z rozumnejsiho mista
		$project->setMandaysLeft(strlen($type));
		$project->setPriority(3);

		$this->getDoctrine()->getManager()->persist($project);
		$this->getDoctrine()->getManager()->persist($region);
		$this->getDoctrine()->getManager()->flush();
		return $this->redirectToRoute('human_dashboard', [
			'human' => $human->getId(),
		]);
	}

	/**
	 * @Route("/screen/{regionUuid}/{human}", name="region_deposit_screening")
	 */
	public function depositScreeningAction($regionUuid, Entity\Human $human, Request $request)
	{
		$region = $this->getDoctrine()->getRepository(Entity\Planet\Region::class)->getByUuid($regionUuid);

		srand($regionUuid);
		$deposits = [];
		if (random_int(1, 10) == 1) {
			$lightDeposit = new Entity\Planet\OreDeposit();
			$lightDeposit->setType('ironOre');
			$lightDeposit->setAmount(random_int(50, 100));
			$lightDeposit->setQuality(100);
			$lightDeposit->setRegion($region);
			$deposits[] = $lightDeposit;
			$heavyDeposit = new Entity\Planet\OreDeposit();
			$heavyDeposit->setType('ironOre');
			$heavyDeposit->setAmount(random_int(200, 1000));
			$heavyDeposit->setQuality(10);
			$heavyDeposit->setRegion($region);
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
			$lightDeposit->setRegion($region);
			$deposits[] = $lightDeposit;
			$heavyDeposit = new Entity\Planet\OreDeposit();
			$heavyDeposit->setType('oil');
			$heavyDeposit->setAmount(random_int(1000, 10000));
			$heavyDeposit->setQuality(10);
			$heavyDeposit->setRegion($region);
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
