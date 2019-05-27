<?php

namespace PlanetBundle\Controller;

use AppBundle\Entity\Human\EventDataTypeEnum;
use AppBundle\Entity\Human\EventTypeEnum;
use AppBundle\Fixture\ResourceAndBlueprintFixture;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use PlanetBundle\Entity as PlanetEntity;
use AppBundle\Entity as GlobalEntity;

/**
 * @Route(path="human")
 */
class HumanController extends BasePlanetController
{
    /**
	 * @Route("/incarnation-list/{soul}", name="human_incarnation_list")
	 */
	public function incarnationListAction(GlobalEntity\Soul $soul, Request $request)
	{
		$humans = $this->getDoctrine()
			->getRepository(PlanetEntity\Human::class)
			->findByAvailableChildren();
		return $this->render('Human/incarnation-list.html.twig', [
			'soul' => $soul,
			'humans' => $humans,
		]);
	}

	/**
	 * @Route("/connect/{soul}/{human}", name="human_connect")
	 */
	public function connectAction(GlobalEntity\Soul $soul, PlanetEntity\Human $human, Request $request)
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

        $this->createEvent(EventTypeEnum::SOUL_HUMAN_CONNECTION, [
        ]);

		return $this->forward('AppBundle:Gamer:dashboard');
	}

	/**
	 * @Route("/", name="human_dashboard")
	 */
	public function dashboardAction(Request $request)
	{
		return $this->redirectToRoute('map_dashboard');
	}
}
