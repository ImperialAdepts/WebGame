<?php

namespace PlanetBundle\Controller;

use AppBundle\Builder\PlanetBuilder;
use AppBundle\Descriptor\TimeTransformator;
use AppBundle\Entity\Human;
use AppBundle\Entity\Human\EventDataTypeEnum;
use AppBundle\Entity\Human\EventTypeEnum;
use AppBundle\Entity\SolarSystem\Planet;
use PlanetBundle\Concept\Food;
use PlanetBundle\Entity as PlanetEntity;
use AppBundle\Fixture\ResourceAndBlueprintFixture;
use PlanetBundle\UseCase\LandBuilding;
use PlanetBundle\UseCase\Portable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Tracy\Debugger;

/**
 * @Route(path="planet-{planet}/map")
 */
class MapController extends BasePlanetController
{
    public function init()
    {
        parent::init();

        $settlementId = $this->get('request_stack')->getCurrentRequest()->get('settlement');
        if ($settlementId != null) {
            $settlement = $this->get('repo_settlement')->find($settlementId);
        } else {
            $settlements = $this->get('repo_settlement')->getAll();
            $settlement = array_pop($settlements);
//            $settlement = $this->human->getCurrentPeakPosition()->getSettlement();
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
	 * @Route("/", name="map_dashboard")
	 */
	public function dashboardAction(Request $request)
	{
		return $this->render('Map/overview.html.twig', [
		    'settlement' => $this->getHuman()->getCurrentPeakPosition()->getSettlement(),
			'human' => $this->getHuman(),
		]);
	}

    /**
     * @Route("/ajax", name="map_ajax_data")
     */
    public function mapAjaxAction(Request $request)
    {
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

            $p->projection = $this->computeProjection($peak->getXcoord(), $peak->getYcoord(), $peak->getHeight());

            if ($peak->getSettlement() != null && $peak == $peak->getSettlement()->getAdministrativeCenter()) {
                $p->administrativeCenter = $this->computeProjection($peak->getXcoord(), $peak->getYcoord(), $peak->getHeight(), 5);
            }

            if ($peak->getSettlement() != null && $peak == $peak->getSettlement()->getTradeCenter()) {
                $p->tradeCenter = $this->computeProjection($peak->getXcoord(), $peak->getYcoord(), $peak->getHeight(), 2);
            }
            $p->statistics = $this->getStatisticsProjections($peak->getXcoord(), $peak->getYcoord(), $peak->getDeposit());

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

            $regionCenter = new \stdClass();
            $regionCenter->x = ($region->getPeakLeft()->getXcoord() + $region->getPeakRight()->getXcoord() + $region->getPeakCenter()->getXcoord())/3;
            $regionCenter->y = ($region->getPeakLeft()->getYcoord() + $region->getPeakRight()->getYcoord() + $region->getPeakCenter()->getYcoord())/3;

            if ($region->getSettlement() !== null) {
                $r->settlement = new \stdClass();
                $r->settlement->owner = $region->getSettlement()->getOwner()->getId();
                $r->settlement->isMine = $region->getSettlement()->getOwner() == $this->getHuman()
                    || $region->getSettlement()->getManager() == $this->getHuman();

                $r->settlement->borderPeaks[] = $this->computeProjection($region->getPeakCenter()->getXcoord(), $region->getPeakCenter()->getYcoord(), $region->getPeakCenter()->getHeight(), 3);
                $r->settlement->borderPeaks[] = $this->computeProjection($region->getPeakLeft()->getXcoord(), $region->getPeakLeft()->getYcoord(), $region->getPeakLeft()->getHeight(), 3);
                $r->settlement->borderPeaks[] = $this->computeProjection($region->getPeakRight()->getXcoord(), $region->getPeakRight()->getYcoord(), $region->getPeakRight()->getHeight(), 3);
            }
            $r->statistics = $this->getStatisticsProjections($regionCenter->x, $regionCenter->y, $region->getDeposit());
            $regions[] = $r;
        }

        $json = [
            'planetDiameter' => $this->getPlanet()->getDiameter(),
            'peaks' => $peaks,
            'regions' => $regions,
        ];
        return new JsonResponse($json);
    }

    private function computeProjection($xcoord, $ycoord, $surfaceHeight = 0, $visualHeight = 0) {
        $heightDegrees = 360 + 90*$ycoord/($this->getPlanet()->getSurfaceGranularity());
        $widthDegrees = 360*$xcoord/$this->getPlanet()->getCoordsWidthLength($ycoord);

        $peakRadius = 100 + 2*$surfaceHeight/(1000*$this->getPlanet()->getDiameter()) + $visualHeight;
        $projection = new \stdClass();
        $projection->x = $peakRadius * cos(deg2rad($heightDegrees)) * cos(deg2rad($widthDegrees));
        $projection->z = $peakRadius * cos(deg2rad($heightDegrees)) * sin(deg2rad($widthDegrees));
        $projection->y = $peakRadius * sin(deg2rad($heightDegrees));
        return $projection;
    }

    private function getStatisticsProjections($coordx, $coordy, PlanetEntity\Deposit $deposit = null) {
        $statistics = $this->createStatistics($deposit);
        $bars = [];
        $statCount = 0;
        $colors = ["#00F", "#9F9", "#0F0", "#A33", "#555"];
        $statisticCounter = 0;
        foreach ($statistics as $name => $statistic) {
            if ($statistic >= 1) {
                $value = 1+log($statistic, 2);
            } else {
                $value = 0;
            }
            $bar = new \stdClass();
            $bar->base = $this->computeProjection($coordx, $coordy, 0, $statCount);
            $bar->top = $this->computeProjection($coordx, $coordy, 0, $statCount += $value);
            $bar->color = $colors[$statisticCounter++ % count($statistics)];
            $bars[] = $bar;
        }
        return $bars;
    }

    private function createStatistics(PlanetEntity\Deposit $deposit = null) {
        srand(12548);
        if ($deposit == null) {
            return [];
        }

        $statistics = [];
        $statistics['objectCount'] = PlanetEntity\Deposit::sumAmounts($deposit->getResourceDescriptors());
        $statistics['foodCount'] = PlanetEntity\Deposit::sumAmounts($deposit->filterByConcept(Food::class));
        $statistics['buildings'] = PlanetEntity\Deposit::sumAmounts($deposit->filterByConcept(LandBuilding::class));
        $statistics['portables'] = PlanetEntity\Deposit::sumAmounts($deposit->filterByConcept(Portable::class));
        return $statistics;
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
    public function currentPlanetInfoAction(Planet $planet, Request $request)
    {
        $currentPhase = TimeTransformator::timestampToPhase($this->getPlanet(), time());
        return $this->render('Map/planet_info.html.twig', [
            'planet' => $planet,
            'endphase' => TimeTransformator::phaseToTimestamp($planet, $currentPhase+1),
        ]);
    }

	/**
	 * @Route("/newcolony/{administrativeCenter}", name="map_newcolony")
	 */
	public function newColonyAction(PlanetEntity\Peak $administrativeCenter, Request $request)
	{
		$builder = new \AppBundle\Builder\PlanetBuilder($this->getDoctrine()->getManager(), $this->getDoctrine()->getManager('planet'), $this->getParameter('default_colonization_packs'));
		$builder->newColony($administrativeCenter, $this->getHuman(), 'simple');
        $this->getDoctrine()->getManager()->flush();

        $this->createEvent(EventTypeEnum::SETTLEMENT_COLONIZATION, [
            EventDataTypeEnum::PEAK => $administrativeCenter,
        ]);

		return $this->redirectToRoute('settlement_dashboard', [
            'planet' => $this->planet->getId(),
		    'settlement' => $administrativeCenter->getSettlement()->getId(),
		]);
	}
	
}
