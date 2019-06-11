<?php
namespace PlanetBundle\Builder;

use AppBundle\Entity\Blueprint;
use AppBundle\Entity\Human;
use AppBundle\Entity\Human\Event;
use AppBundle\Entity\Job\ProduceJob;
use AppBundle\Entity\SolarSystem\Planet;
use AppBundle\LoggedUserSettings;
use AppBundle\PlanetConnection\DynamicPlanetConnector;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use PlanetBundle\Entity\Job\Job;

class EventBuilder
{
    /** @var ObjectManager */
    protected $generalEntityManager;

    /** @var DynamicPlanetConnector */
    protected $dynamicPlanetConnector;

    /**
     * MaintainerEventBuilder constructor.
     * @param ObjectManager $generalEntityManager
     * @param DynamicPlanetConnector $planetConnector
     */
    public function __construct(ObjectManager $generalEntityManager, DynamicPlanetConnector $planetConnector)
    {
        $this->generalEntityManager = $generalEntityManager;
        $this->dynamicPlanetConnector = $planetConnector;
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
        $event->setPlanet($this->dynamicPlanetConnector->getPlanet());
        $event->setPlanetPhase($this->dynamicPlanetConnector->getPlanet()->getLastPhaseUpdate());
        $event->setTime(time());
        $event->setDescriptionData($eventData);
        $event->setHuman($supervisor);

        $this->generalEntityManager->persist($event);
        return $event;
    }
}