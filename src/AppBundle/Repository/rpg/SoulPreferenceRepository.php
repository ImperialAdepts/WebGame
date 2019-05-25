<?php
namespace AppBundle\Repository\rpg;

use AppBundle\Entity; use PlanetBundle\Entity as PlanetEntity;

class SoulPreferenceRepository extends \Doctrine\ORM\EntityRepository
{
//    public function findByLogin($login)
//    {
//        return $this->getEntityManager()
//            ->createQuery(
//                'SELECT g FROM AppBundle:Gamer g WHERE g.login = :login ORDER BY g.login ASC', $login
//            )
//            ->setParameter('login', $login)
//            ->getOneOrNullResult();
//    }
//
//    public function getAll()
//    {
//        return $this->getEntityManager()
//            ->createQuery(
//                'SELECT g FROM AppBundle:Gamer g ORDER BY g.login ASC'
//            )
//            ->getResult();
//    }

    public function getAvailableTypes(Entity\Soul $soul) {
        return [
        ];
    }
}