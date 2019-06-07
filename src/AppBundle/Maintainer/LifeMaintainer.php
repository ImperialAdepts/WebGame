<?php
namespace AppBundle\Maintainer;

use AppBundle\Entity\Human;

class LifeMaintainer
{
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
}