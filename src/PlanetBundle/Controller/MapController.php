<?php

namespace PlanetBundle\Controller;

use AppBundle\Builder\PlanetBuilder;
use AppBundle\Descriptor\TimeTransformator;
use PlanetBundle\Entity as PlanetEntity;
use AppBundle\Fixture\ResourceAndBlueprintFixture;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tracy\Debugger;

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
        $mapRepo = $this->getDoctrine()->getManager('planet')->getRepository(PlanetEntity\Region::class);
	    $regions = $mapRepo->getRegionNeighbourhood($centralRegion);

	    $leftPeaks = $mapRepo->getPeaksLeftOf($centralRegion->getPeakCenter(), 20);
	    $rightPeaks = $mapRepo->getPeaksRightOf($centralRegion->getPeakCenter(), 20);

        $leftRegions = $this->completeLines($leftPeaks);
        $rightRegions = $this->completeLines($rightPeaks);

		$blueprintsByRegions = [];
	    /** @var PlanetBuilder $builder */
	    $builder = $this->get('planet_builder');
	    /** @var PlanetEntity\Region $region */
        foreach ($regions as $region) {
	        $blueprintsByRegions[$region->getCoords()] = $builder->getAvailableBlueprints($region, $this->getHuman());
        }
		return $this->render('Map/overview.html.twig', [
		    'settlement' => $this->getHuman()->getCurrentPosition(),
			'human' => $this->getHuman(),
			'centralRegion' => $centralRegion,
			'nextRegions' => $regions,
			'buildingBlueprints' => $blueprintsByRegions,
            'rights' => $rightRegions,
            'lefts' => $leftRegions,
		]);
	}

    /**
     * @param PlanetEntity\Peak[] $peaks
     * @return PlanetEntity\Region[]
     */
	private function completeLines($peaks) {
        $mapRepo = $this->getDoctrine()->getManager('planet')->getRepository(PlanetEntity\Region::class);
	    $lines = [];
        /** @var PlanetEntity\Peak $peak */
        foreach ($peaks as $peak) {
            $lines[$peak->getXcoord()][] = $peak;
        }
        $regionLines = [];
	    foreach ($lines as $xcoord => $line) {

	        $touples = $this->getTouples($line);

            if (isset($lines[$xcoord - 1])) {
                $topRegions = [];
                /** @var PlanetEntity\Peak $p */
                foreach ($touples as $touple) {
                    list($leftPeak, $rightPeak) = $touple;

                    foreach ($lines[$xcoord - 1] as $topPeak) {
                        $region = $mapRepo->findByPeaks($topPeak, $leftPeak, $rightPeak);
                        if ($region !== null) {
                            $topRegions[] = $region;
                        }
                    }
                    $regionLines['t' . $xcoord] = $topRegions;
                }
            }

            if (isset($lines[$xcoord + 1])) {
                $bottomRegions = [];
                /** @var PlanetEntity\Peak $p */
                foreach ($touples as $touple) {
                    list($leftPeak, $rightPeak) = $touple;

                    foreach ($lines[$xcoord + 1] as $bottomPeak) {
                        $region = $mapRepo->findByPeaks($bottomPeak, $leftPeak, $rightPeak);
                        if ($region !== null) {
                            $bottomRegions[] = $region;
                        }
                    }
                    $regionLines['b' . $xcoord] = $bottomRegions;
                }
            }
        }
        return $regionLines;
    }

    private function getTouples($peakLine) {
        $touples = [];
        $previousPeak = null;
        foreach ($peakLine as $peak) {
            if ($previousPeak == null) {
                $previousPeak = $peak;
            }
            $touples[] = [$previousPeak, $peak];
            $previousPeak = $peak;
        }
	    return $touples;
    }

    /**
     * @Route("/planet_info", name="map_current_planet")
     */
    public function currentPlanetInfoAction(Request $request)
    {
        $currentPhase = TimeTransformator::timestampToPhase($this->getPlanet(), time());
        return $this->render('Map/planet_info.html.twig', [
            'planet' => $this->getPlanet(),
            'endphase' => TimeTransformator::phaseToTimestamp($this->getPlanet(), $currentPhase+1),
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
