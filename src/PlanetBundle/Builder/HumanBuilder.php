<?php

namespace PlanetBundle\Builder;

use AppBundle\Entity\Human;
use AppBundle\PlanetConnection\DynamicPlanetConnector;
use AppBundle\Repository\HumanRepository;
use Doctrine\Common\Persistence\ObjectManager;
use PlanetBundle\Entity as PlanetEntity;

class HumanBuilder
{
    /** @var ObjectManager */
    private $generalEntityManager;

    /** @var ObjectManager */
    private $planetEntityManager;

    /** @var HumanRepository */
    private $generalHumanRepository;

    /**
     * HumanBuilder constructor.
     * @param ObjectManager $generalEntityManager
     * @param ObjectManager $planetEntityManager
     * @param HumanRepository $generalHumanRepository
     */
    public function __construct(ObjectManager $generalEntityManager, ObjectManager $planetEntityManager, HumanRepository $generalHumanRepository)
    {
        $this->generalEntityManager = $generalEntityManager;
        $this->planetEntityManager = $planetEntityManager;
        $this->generalHumanRepository = $generalHumanRepository;
    }

    public function create(Human $mother, Human $father = null) {
        if ($mother->getPlanet() !== DynamicPlanetConnector::$PLANET) {
            throw new \Exception("Creating human on different planet then mother is.");
        }

        /** @var PlanetEntity\Human $planetMother */
        $planetMother = $this->planetEntityManager->getRepository(PlanetEntity\Human::class)->findOneBy([
            'globalHumanId' => $mother->getId(),
        ]);

        $child = new Human();
        $child->setName('noname');
        $child->setBornPlanet($mother->getPlanet());
        $child->setBornPhase($mother->getPlanet()->getLastPhaseUpdate());
        $child->setPlanet($mother->getPlanet());
        $child->setMotherHuman($mother);
        $child->setFatherHuman(null);

        $this->generalEntityManager->persist($child);
        $this->generalEntityManager->flush();

        $planetOffspring = new PlanetEntity\Human();
        $planetOffspring->setGlobalHumanId($child->getId());
        $planetOffspring->setCurrentPeakPosition($planetMother->getCurrentPeakPosition());

        $this->planetEntityManager->persist($planetOffspring);
        $this->planetEntityManager->flush();

        return $child;
    }
}