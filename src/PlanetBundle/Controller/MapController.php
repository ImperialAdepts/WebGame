<?php

namespace PlanetBundle\Controller;

use AppBundle\Builder\PlanetBuilder;
use PlanetBundle\Entity as PlanetEntity;
use AppBundle\Fixture\ResourceAndBlueprintFixture;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="map")
 */
class MapController extends BasePlanetController
{
	/**
	 * @Route("/", name="map_dashboard")
	 */
	public function dashboardAction(Request $request)
	{
        $centralRegion = $this->getHuman()->getCurrentPosition()->getMainRegion();

	    $regions = $this->getDoctrine()->getManager('planet')->getRepository(PlanetEntity\Region::class)->getRegionNeighbourhood($centralRegion);

		$blueprintsByRegions = [];
	    /** @var PlanetBuilder $builder */
	    $builder = $this->get('planet_builder');
	    /** @var PlanetEntity\Region $region */
        foreach ($regions as $region) {
	        $blueprintsByRegions[$region->getCoords()] = $builder->getAvailableBlueprints($region, $this->getHuman());
        }
		return $this->render('Map/overview.html.twig', [
			'human' => $this->getHuman(),
			'centralRegion' => $centralRegion,
			'nextRegions' => $regions,
			'buildingBlueprints' => $blueprintsByRegions,
		]);
	}

	/**
	 * @Route("/newcolony/{regionC}_{regionL}_{regionR}", name="map_newcolony")
	 */
	public function newColonyAction(PlanetEntity\Peak $regionC, PlanetEntity\Peak $regionL, PlanetEntity\Peak $regionR, Request $request)
	{
        /** @var PlanetEntity\Region $region */
        $region = $this->getDoctrine()->getRepository(PlanetEntity\Region::class)->findByPeaks($regionC, $regionL, $regionR);
		$builder = new \AppBundle\Builder\PlanetBuilder($this->getDoctrine()->getManager(), $this->getParameter('default_colonization_packs'));
		$builder->newColony($region, $this->getHuman(), 'simple');
        $this->getDoctrine()->getManager()->flush();

		return $this->redirectToRoute('settlement_dashboard', [
		    'settlement' => $region->getSettlement()->getId(),
		]);
	}
	
}
