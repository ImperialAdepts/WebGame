<?php
namespace AppBundle\Repository\Human;

use AppBundle\Entity;
use PlanetBundle\Entity as PlanetEntity;

class EventRepository extends \Doctrine\ORM\EntityRepository
{
    public function getThisPhaseReport(Entity\Human $human) {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT e FROM AppBundle:Human\Event e WHERE e.human = :human ORDER BY e.time DESC'
            )
            ->setParameter('human', $human)
            ->getResult();
    }
}