<?php

namespace PlanetBundle\Controller;

use AppBundle\Builder\PlanetBuilder;
use AppBundle\Descriptor\TimeTransformator;
use PlanetBundle\Entity as PlanetEntity;
use AppBundle\Fixture\ResourceAndBlueprintFixture;
use Symfony\Component\HttpFoundation\JsonResponse;
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
		return $this->render('Map/overview.html.twig', [
		    'settlement' => $this->getHuman()->getCurrentPosition(),
			'human' => $this->getHuman(),
		]);
	}

    /**
     * @Route("/ajax", name="map_ajax_data")
     */
    public function mapAjaxAction(Request $request)
    {
        $centralRegion = $this->getHuman()->getCurrentPosition()->getMainRegion();
        $peakRepo = $this->getDoctrine()->getManager('planet')->getRepository(PlanetEntity\Peak::class);
        $mapRepo = $this->getDoctrine()->getManager('planet')->getRepository(PlanetEntity\Region::class);
        $regions = [];
        $peaks = [];

        /** @var PlanetEntity\Peak $peak */
        foreach($peakRepo->findAll() as $peak) {
            $heightDegrees = 360 + 90*$peak->getYcoord()/($this->getPlanet()->getSurfaceGranularity());
            $widthDegrees = 360*$peak->getXcoord()/$this->getPlanet()->getCoordsWidthLength($peak->getYcoord());

            $p = new \stdClass();
            $p->x = $peak->getXcoord();
            $p->y = $peak->getYcoord();
            $p->h = $heightDegrees;
            $p->w = $widthDegrees;
            $p->height = $peak->getHeight();

            $peakRadius = $this->getPlanet()->getDiameter()/2 + $peak->getHeight()/1000;
            $peakRadius *= 10;
            $p->projection = new \stdClass();
            $p->projection->x = $peakRadius * cos(deg2rad($heightDegrees)) * cos(deg2rad($widthDegrees));
            $p->projection->z = $peakRadius * cos(deg2rad($heightDegrees)) * sin(deg2rad($widthDegrees));
            $p->projection->y = $peakRadius * sin(deg2rad($heightDegrees));
            $peaks[$peak->getId()] = $p;
        }
        /** @var PlanetEntity\Region $region */
        foreach($mapRepo->findAll() as $region) {
            $r = new \stdClass();
            $r->peaks = [
                $region->getPeakLeft()->getId(),
                $region->getPeakRight()->getId(),
                $region->getPeakCenter()->getId(),
            ];
            $r->type = $region->getTerrainType();
            $regions[] = $r;
        }

        $json = [
            'planetDiameter' => $this->getPlanet()->getDiameter(),
            'peaks' => $peaks,
            'regions' => $regions,
        ];
        return new JsonResponse($json);
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
