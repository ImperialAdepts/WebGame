<?php
namespace AppBundle\Repository;

use AppBundle\Entity;

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

	}
}