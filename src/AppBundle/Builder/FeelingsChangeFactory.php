<?php

namespace AppBundle\Builder;

use AppBundle\Entity\Human;
use AppBundle\Entity\Human\Event;
use AppBundle\Entity\Human\FeelingChange;
use PlanetBundle\Builder\RegionBuilder;
use PlanetBundle\Entity\Resource\Blueprint;
use Doctrine\ORM\EntityManager;

class FeelingsChangeFactory
{
    /**
     * @param Event $event
     * @param Human|null $humanView
     * @return FeelingChange
     */
    public function create(Event $event, Human $humanView = null) {
        if ($humanView == null) {
            $event->getHuman()->getFeelings()->change(100-random_int(0, 200), $event->getDescription(), $event->getDescriptionData(), $event);
        } else {
            $humanView->getFeelings()->change(100-random_int(0, 200), $event->getDescription(), $event->getDescriptionData(), $event);
        }
    }
}