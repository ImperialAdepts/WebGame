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
 * @Route(path="planet")
 */
class BasePlanetController extends Controller
{
    /** @var Entity\SolarSystem\Planet */
	protected $planet;
	/** @var PlanetEntity\Human */
	protected $human;
	/** @var Entity\Human */
	protected $globalHuman;

    public function init() {
        $this->globalHuman = $this->get('logged_user_settings')->getHuman();
        $this->planet = $this->globalHuman->getPlanet();

        $this->container->get('dynamic_planet_connector')->setPlanet($this->planet, true);
        $this->human = $this->getDoctrine()->getManager('planet')
            ->getRepository(PlanetEntity\Human::class)->getByGlobalHuman($this->globalHuman);

        $this->get('twig')->addGlobal('planet', $this->planet);
        $this->get('twig')->addGlobal('settlement', $this->human->getCurrentPosition());
        $events = $this->getDoctrine()->getManager()
            ->getRepository(Entity\Human\Event::class)->getThisPhaseReport($this->globalHuman);
        $this->get('twig')->addGlobal('events', $events);

        if ($this->human === null) {
            throw new NotFoundHttpException("Human was not found on this planet");
        }
    }

    /**
     * @param $eventNme
     * @param array $eventData
     * @return Entity\Human\Event
     */
    public function createEvent($eventNme, array $eventData = []) {
        $event = new Entity\Human\Event();
        $event->setDescription($eventNme);
        $event->setPlanet($this->planet);
        $event->setPlanetPhase($this->planet->getLastPhaseUpdate());
        $event->setTime(time());
        $event->setDescriptionData($eventData);
        $event->setHuman($this->globalHuman);

        $this->getDoctrine()->getManager()->persist($event);
        $this->getDoctrine()->getManager()->flush();
        return $event;
    }

    /**
     * @return Entity\SolarSystem\Planet
     */
    public function getPlanet()
    {
        return $this->planet;
    }

    /**
     * @return PlanetEntity\Human
     */
    public function getHuman()
    {
        return $this->human;
    }

    /**
     * @return Entity\Human
     */
    public function getGlobalHuman()
    {
        return $this->globalHuman;
    }

}
