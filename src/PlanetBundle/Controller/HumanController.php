<?php

namespace PlanetBundle\Controller;

use AppBundle\Builder\PlanetBuilder;
use AppBundle\Entity;
use PlanetBundle\Entity as PlanetEntity;
use AppBundle\Fixture\ResourceAndBlueprintFixture;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Tracy\Debugger;

/**
 * @Route(path="human")
 */
class HumanController extends Controller
{
    /**
	 * @Route("/incarnation-list/{soul}", name="human_incarnation_list")
	 */
	public function incarnationListAction(Entity\Soul $soul, Request $request)
	{
		if ($soul->getIncarnations() != null) {
			throw new \InvalidArgumentException("This soul already have human incarnation");
		}

		$humans = $this->getDoctrine()
			->getRepository(Entity\Human::class)
			->findByAvailableChildren();
		return $this->render('Human/incarnation-list.html.twig', [
			'soul' => $soul,
			'humans' => $humans,
		]);
	}

	/**
	 * @Route("/connect/{soul}/{human}", name="human_connect")
	 */
	public function connectAction(Entity\Soul $soul, Entity\Human $human, Request $request)
	{
		// TODO: zkontrolovat jestli to zkousi spravny gamer
		if ($soul->getIncarnations() != null || $human->getSoul() != null) {
			throw new \InvalidArgumentException("This soul and human can't be connected, not empty");
		}
		$soul->setIncarnations($human);
		$human->setSoul($soul);
		$this->getDoctrine()->getManager()->persist($soul);
		$this->getDoctrine()->getManager()->persist($human);
		$this->getDoctrine()->getManager()->flush();

		return $this->forward('AppBundle:Gamer:dashboard');
	}

    /**
     * @Route("/play-as-{human}", name="human_play_as")
     */
    public function playAsHumanAction(Entity\Human $human, Request $request)
    {
        $this->get('logged_user_settings')->setHuman($human);
        return $this->redirectToRoute('human_dashboard', [
            'human' => $human->getId(),
        ]);
    }

	/**
	 * @Route("/", name="human_dashboard")
	 */
	public function dashboardAction(Request $request)
	{
        $globalHuman = $this->get('logged_user_settings')->getHuman();
        $localHuman = $this->getDoctrine()->getManager('planet')
            ->getRepository(PlanetEntity\Human::class)->getByGlobalHuman($globalHuman);

        if ($localHuman === null) {
            Debugger::dump($globalHuman);
            throw new NotFoundHttpException("Human was not found on this planet");
        }

	    $regions = $localHuman->getCurrentPosition()->getRegions();
	    foreach ($regions as $region) {
	        $centralRegion = $region;
	        break;
        }

	    $regions = $this->getDoctrine()->getManager('planet')->getRepository(PlanetEntity\Region::class)->getRegionNeighbourhood($centralRegion);

		$blueprintsByRegions = [];
	    /** @var PlanetBuilder $builder */
	    $builder = $this->get('planet_builder');
	    /** @var PlanetEntity\Region $region */
        foreach ($regions as $region) {
	        $blueprintsByRegions[$region->getCoords()] = $builder->getAvailableBlueprints($region, $localHuman);
        }
		return $this->render('Human/dashboard.html.twig', [
			'human' => $globalHuman,
			'centralRegion' => $centralRegion,
			'nextRegions' => $regions,
			'buildingBlueprints' => $blueprintsByRegions,
		]);
	}

	/**
	 * @Route("/newcolony/{regionC}_{regionL}_{regionR}", name="human_newcolony")
	 */
	public function newColonyAction(PlanetEntity\Peak $regionC, PlanetEntity\Peak $regionL, PlanetEntity\Peak $regionR, Request $request)
	{
        $human = $this->get('logged_user_settings')->getHuman();
        /** @var PlanetEntity\Region $region */
        $region = $this->getDoctrine()->getRepository(PlanetEntity\Region::class)->findByPeaks($regionC, $regionL, $regionR);
		$builder = new \AppBundle\Builder\PlanetBuilder($this->getDoctrine()->getManager(), $this->getParameter('default_colonization_packs'));
		$builder->newColony($region, $human, 'simple');
        $this->getDoctrine()->getManager()->flush();

		return $this->redirectToRoute('settlement_dashboard', [
		    'settlement' => $region->getSettlement()->getId(),
		]);
	}
	
}
