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
class HumanController extends BasePlanetController
{
    /**
	 * @Route("/incarnation-list/{soul}", name="human_incarnation_list")
	 */
	public function incarnationListAction(Entity\Soul $soul, Request $request)
	{
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
		return $this->redirectToRoute('map_dashboard');
	}
}
