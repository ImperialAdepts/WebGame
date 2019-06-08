<?php
namespace AppBundle\Maintainer;

use AppBundle\Entity\Human;
use AppBundle\Entity\SolarSystem\Planet;
use AppBundle\PlanetConnection\DynamicPlanetConnector;
use Doctrine\ORM\EntityManager;
use PlanetBundle\Entity as PlanetEntity;

class LifeMaintainer
{
    /** @var EntityManager */
    private $generalEntityManager;

    /** @var EntityManager */
    private $planetEntityManager;

    /** @var Planet */
    private $planet;

    /**
     * JobMaintainer constructor.
     * @param EntityManager $generalEntityManager
     * @param EntityManager $planetEntityManager
     * @param Planet $planet
     */
    public function __construct(EntityManager $generalEntityManager, EntityManager $planetEntityManager, Planet $planet)
    {
        $this->generalEntityManager = $generalEntityManager;
        $this->planetEntityManager = $planetEntityManager;
        $this->planet = $planet;
    }

    /**
     * @return int promile
     */
    public function getDeathByAgeProbability(Human $human) {

        if ($human->getAge() <= 40) {
            return 0;
        }
        return ($human->getAge() - 40);
    }

    public function kill(Human $human) {
        $human->setDeathTime(time());

        $this->inheritTitles($human);

        $human->setTitle(null);
        $human->setTitles([]);
    }

    private function inheritTitles(Human $human)
    {
        foreach ($human->getTitles() as $title) {
            $this->inheritTitle($title);
        }
    }

    private function inheritTitle(Human\Title $title)
    {
        $heir = $title->getHeir();
        if ($heir != null) {
            $title->setHumanHolder($heir);
            $heir->addTitle($title);
            if ($heir->getTitle() == null) {
                $heir->setTitle($title);
            }
        }
        // TODO: povznest nahodneho lowborna do slechtickeho titulu => vyrobit noveho humana
    }

    public function makeOffspring(Human $mother, Human $father = null) {
        if ($mother->getPlanet() !== DynamicPlanetConnector::$PLANET) {
            throw new \Exception("Creating offspring on different planet then mother is.");
        }

        /** @var PlanetEntity\Human $planetMother */
        $planetMother = $this->planetEntityManager->getRepository(PlanetEntity\Human::class)->findOneBy([
            'globalHumanId' => $mother->getId(),
        ]);

        $offspring = new Human();
        $offspring->setName($mother->getName(). ' '.random_int(0, 20));
        $offspring->setBornPlanet($mother->getPlanet());
        $offspring->setBornPhase($mother->getPlanet()->getLastPhaseUpdate());
        $offspring->setPlanet($mother->getPlanet());
        $offspring->setMotherHuman($mother);
        $offspring->setFatherHuman(null);

        $this->generalEntityManager->persist($offspring);
        $this->generalEntityManager->flush();

        $planetOffspring = new PlanetEntity\Human();
        $planetOffspring->setGlobalHumanId($offspring->getId());
        $planetOffspring->setCurrentPeakPosition($planetMother->getCurrentPeakPosition());

        $this->planetEntityManager->persist($planetOffspring);
        $this->planetEntityManager->flush();

        return $offspring;
    }
}