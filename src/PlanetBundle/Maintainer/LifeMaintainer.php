<?php
namespace PlanetBundle\Maintainer;

use AppBundle\Entity\Human;
use PlanetBundle\Builder\EventBuilder;
use PlanetBundle\Builder\HumanBuilder;
use PlanetBundle\Entity as PlanetEntity;

class LifeMaintainer
{
    /** @var HumanBuilder */
    private $humanBuilder;

    /** @var EventBuilder */
    private $eventBuilder;

    /**
     * LifeMaintainer constructor.
     * @param HumanBuilder $humanBuilder
     * @param EventBuilder $eventBuilder
     */
    public function __construct(HumanBuilder $humanBuilder, EventBuilder $eventBuilder)
    {
        $this->humanBuilder = $humanBuilder;
        $this->eventBuilder = $eventBuilder;
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

        $this->eventBuilder->create(Human\EventTypeEnum::HUMAN_DEATH, $human, [
            Human\EventDataTypeEnum::DESCRIPTION => 'death by age',
        ]);

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

            $this->eventBuilder->create(Human\EventTypeEnum::HUMAN_INHERITANCE, $heir, [
                Human\EventDataTypeEnum::TITLE => $title,
            ]);
        }
        // TODO: povznest nahodneho lowborna do slechtickeho titulu => vyrobit noveho humana
    }

    public function makeOffspring(Human $mother, Human $father = null) {
        $offspring = $this->humanBuilder->create($mother, $father);

        $offspring->setName($mother->getName(). ' '.random_int(0, 20));

        $this->eventBuilder->create(Human\EventTypeEnum::HUMAN_BIRTH, $offspring, []);
        $this->eventBuilder->create(Human\EventTypeEnum::HUMAN_OFFSPRING_BORN, $mother, [
            Human\EventDataTypeEnum::HUMAN => $offspring->getId(),
        ]);
        if ($father) {
            $this->eventBuilder->create(Human\EventTypeEnum::HUMAN_OFFSPRING_BORN, $father, [
                Human\EventDataTypeEnum::HUMAN => $offspring->getId(),
            ]);
        }

        return $offspring;
    }
}