<?php
namespace AppBundle\Repository\rpg;

use AppBundle\Entity; use PlanetBundle\Entity as PlanetEntity;

class HumanPreferenceRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAvailableTypes(Entity\Human $soul) {
        return [
        ];
    }
}