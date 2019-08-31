<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Human\EventTypeEnum;
use AppBundle\EnumAlignmentType;
use PlanetBundle\Maintainer\LifeMaintainer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity;
use PlanetBundle\Entity as PlanetEntity;

/**
 * @Route(path="human")
 */
class HumanController extends Controller
{
	/**
	 * @Route("/login/{login}", name="gamer_login")
	 */
	public function loginAction($login, Request $request)
	{
	    $gamer = $this->getDoctrine()->getManager()->getRepository(Entity\Gamer::class)->findByLogin($login);
	    $this->get('logged_user_settings')->setGamer($gamer);
		return $this->redirectToRoute('gamer_human_selection');
	}

	/**
	 * @Route("/journal", name="human_dashboard")
	 */
	public function dashboardAction()
	{
	    if ($this->get('logged_user_settings')->getHuman() === null) {
            return $this->redirectToRoute('gamer_human_selection');
        }

        $planet = $this->get('logged_user_settings')->getHuman()->getPlanet();

        $this->container->get('dynamic_planet_connector')->setPlanet($planet, true);
        $localHuman = $this->getDoctrine()->getManager('planet')
            ->getRepository(PlanetEntity\Human::class)->getByGlobalHuman($this->get('logged_user_settings')->getHuman());

		return $this->render('Human/dashboard.html.twig', [
		    'human' => $this->get('logged_user_settings')->getHuman(),
            'planetHuman' => $localHuman,
		]);
	}

    /**
     * @Route("/create-children", name="human_create_children")
     */
    public function createChildrenAction()
    {
        if ($this->get('logged_user_settings')->getHuman() === null) {
            return $this->redirectToRoute('gamer_human_selection');
        }
        /** @var Entity\Human $father */
        $mother = $this->get('logged_user_settings')->getHuman();
        $this->container->get('dynamic_planet_connector')->setPlanet($mother->getPlanet(), true);

        $this->get('maintainer_life')->makeOffspring($mother);
        $this->get('doctrine.orm.entity_manager')->flush();

        return $this->redirectToRoute('human_dashboard');
    }

    /**
     * @Route("/suicide", name="human_suicide")
     */
    public function suicideAction()
    {
        if ($this->get('logged_user_settings')->getHuman() === null) {
            return $this->redirectToRoute('gamer_human_selection');
        }
        /** @var Entity\Human $human */
        $human = $this->get('logged_user_settings')->getHuman();
        $this->container->get('dynamic_planet_connector')->setPlanet($human->getPlanet(), true);

        $this->get('maintainer_life')->kill($human);

        $this->getDoctrine()->getManager()->persist($human);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('human_dashboard');
    }

    /**
     * @Route("/happy/{change}", name="human_happy")
     */
    public function happyAction($change, Request $request)
    {
        if ($this->get('logged_user_settings')->getHuman() === null) {
            return $this->redirectToRoute('gamer_human_selection');
        }
        $human = $this->get('logged_user_settings')->getHuman();
        $human->getFeelings()->change($change, "handmade change for test by ".$this->get('logged_user_settings')->getGamer()->getLogin(), [
            'human_cause' => $this->get('logged_user_settings')->getHuman(),
        ]);
        $this->getDoctrine()->getManager()->flush($human->getFeelings());
        return $this->redirectToRoute('human_dashboard');
    }

    /**
     * @Route("/sad/{change}", name="human_sad")
     */
    public function sadAction($change, Request $request)
    {
        if ($this->get('logged_user_settings')->getHuman() === null) {
            return $this->redirectToRoute('gamer_human_selection');
        }
        $human = $this->get('logged_user_settings')->getHuman();
        $human->getFeelings()->change(-1*$change, "handmade change for test by ".$this->get('logged_user_settings')->getGamer()->getLogin(), [
            'human_cause' => $this->get('logged_user_settings')->getHuman(),
        ]);
        $this->getDoctrine()->getManager()->flush($human->getFeelings());
        return $this->redirectToRoute('human_dashboard');
    }

    /**
     * @Route("/play-as-{human}", name="gamer_play_as_human")
     */
    public function playAsHumanAction(Entity\Human $human, Request $request)
    {
        $this->get('logged_user_settings')->setHuman($human);
        return $this->redirectToRoute('human_dashboard');
    }

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
        if ($human->getSoul() != null) {
            throw new \InvalidArgumentException("This soul and human can't be connected, not empty");
        }
        $soul->getIncarnations()->add($human);
        $human->setSoul($soul);
        $this->getDoctrine()->getManager()->persist($soul);
        $this->getDoctrine()->getManager()->persist($human);
        $this->getDoctrine()->getManager()->flush();

        $this->createEvent(EventTypeEnum::SOUL_HUMAN_CONNECTION, $human->getPlanet(), [
        ]);

        return $this->forward('AppBundle:Gamer:dashboard');
    }

    /**
     * @param $eventNme
     * @param array $eventData
     * @return Entity\Human\Event
     */
    public function createEvent($eventNme, Entity\SolarSystem\Planet $planet, array $eventData = []) {
        $event = new Entity\Human\Event();
        $event->setDescription($eventNme);
        $event->setPlanet($planet);
        $event->setPlanetPhase($planet->getLastPhaseUpdate());
        $event->setTime(time());
        $event->setDescriptionData($eventData);
        $event->setHuman($this->globalHuman);

        $this->getDoctrine()->getManager()->persist($event);
        $this->getDoctrine()->getManager()->flush();
        return $event;
    }
}
