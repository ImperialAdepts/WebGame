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

		$settlement = new Entity\Planet\Settlement();
		$settlement->setType($type);
		$settlement->setOwner($human);
		$settlement->setManager($human);
		$region->setSettlement($settlement);
		$this->getDoctrine()->getManager()->persist($settlement);
		$this->getDoctrine()->getManager()->persist($region);
		$this->getDoctrine()->getManager()->flush();
		return $this->redirectToRoute('human_dashboard', [
			'human' => $human->getId(),
		]);
	}


//	/**
//	 * @Route("/{region}", name="region_dashboard")
//	 */
//	public function dashboardAction(Entity\Planet\Region $human, Request $request)
//	{
//		$centralRegion = $this->getDoctrine()->getRepository(Entity\Planet\Region::class)->getByUuid(0);
//		return $this->render('Region/dashboard.html.twig', [
//			'human' => $human,
//			'centralRegion' => $centralRegion,
//			'nextRegions' => $this->getDoctrine()->getRepository(Entity\Planet\Region::class)->getRegionNeighbarhood($centralRegion),
//		]);
//	}
}
