<?php
namespace PlanetBundle\Builder;

use AppBundle\Builder\FeelingsChangeFactory;
use AppBundle\Entity\Human;
use AppBundle\Entity\Human\Event;
use AppBundle\PlanetConnection\DynamicPlanetConnector;
use Doctrine\Common\Persistence\ObjectManager;

class EventBuilder
{
    /** @var ObjectManager */
    protected $generalEntityManager;

    /** @var DynamicPlanetConnector */
    protected $dynamicPlanetConnector;

    /** @var FeelingsChangeFactory */
    private $feelingsChangeFactory;

    /**
     * EventBuilder constructor.
     * @param ObjectManager $generalEntityManager
     * @param DynamicPlanetConnector $dynamicPlanetConnector
     * @param FeelingsChangeFactory $feelingsChangeFactory
     */
    public function __construct(ObjectManager $generalEntityManager, DynamicPlanetConnector $dynamicPlanetConnector, FeelingsChangeFactory $feelingsChangeFactory)
    {
        $this->generalEntityManager = $generalEntityManager;
        $this->dynamicPlanetConnector = $dynamicPlanetConnector;
        $this->feelingsChangeFactory = $feelingsChangeFactory;
    }


    /**
     * @param $eventNme
     * @param Human $supervisor
     * @param array $eventData
     * @return Event
     * @throws \Doctrine\ORM\ORMException
     */
    public function create($eventNme, Human $supervisor, array $eventData = []) {
        $event = new Event();
        $event->setDescription($eventNme);
        $event->setPlanet($supervisor->getPlanet());
        $event->setPlanetPhase($supervisor->getPlanet()->getLastPhaseUpdate());
        $event->setTime(time());
        $event->setDescriptionData($eventData);
        $event->setHuman($supervisor);

        $this->generalEntityManager->persist($event);
        return $event;
    }
}