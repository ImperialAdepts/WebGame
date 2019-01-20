<?php

namespace AppBundle\Controller;

use AppBundle\Builder\PlanetBuilder;
use AppBundle\Entity;
use AppBundle\Fixture\ResourceAndBlueprintFixture;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
	 * @Route("/{human}", name="human_dashboard")
	 */
	public function dashboardAction(Entity\Human $human, Request $request)
	{
	    $regions = $human->getCurrentPosition()->getRegions();
	    foreach ($regions as $region) {
	        $centralRegion = $region;
	        break;
        }

	    $regions = $this->getDoctrine()->getRepository(Entity\Planet\Region::class)->getRegionNeighbarhood($centralRegion);

		$blueprintsByRegions = [];
	    /** @var PlanetBuilder $builder */
	    $builder = $this->get('planet_builder');
	    /** @var Entity\Planet\Region $region */
        foreach ($regions as $region) {
	        $blueprintsByRegions[$region->getCoords()] = $builder->getAvailableBlueprints($region, $human);
        }
		return $this->render('Human/dashboard.html.twig', [
			'human' => $human,
			'centralRegion' => $centralRegion,
			'nextRegions' => $regions,
			'buildingBlueprints' => $blueprintsByRegions,
		]);
	}

	/**
	 * @Route("/{human}/newcolony", name="human_newcolony")
	 */
	public function newColonyAction(Entity\Human $human, Request $request)
	{
		$centralRegion = $this->getDoctrine()->getRepository(Entity\Planet\Region::class)->getByUuid(0);
		$builder = new \AppBundle\Builder\PlanetBuilder($this->getDoctrine()->getManager());
		$builder->newColony($centralRegion, $human);

		return $this->redirectToRoute('human_dashboard', [
			'human' => $human->getId(),
		]);
	}
	
}
