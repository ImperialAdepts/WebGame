<?php
namespace AppBundle\Repository;

use AppBundle\Entity; use PlanetBundle\Entity as PlanetEntity;

class SoulRepository extends \Doctrine\ORM\EntityRepository
{

	public function findAllOrderedByName()
	{
		return $this->getEntityManager()
			->createQuery(
				'SELECT p FROM AppBundle:Soul p ORDER BY p.name ASC'
			)
			->getResult();
	}

	public function getByGamer(Entity\Gamer $gamer)
	{
		return $this->getEntityManager()
			->createQuery(
				'SELECT p FROM AppBundle:Soul p WHERE p.gamer_id = %s ORDER BY p.name ASC', $gamer.getId()
			)
			->getResult();
	}
}