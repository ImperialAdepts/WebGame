<?php
namespace AppBundle\Repository;

use AppBundle\Entity; use PlanetBundle\Entity as PlanetEntity;

class HumanRepository extends \Doctrine\ORM\EntityRepository
{

	public function findByAvailableChildren()
	{
		return $this->getEntityManager()
			->createQuery(
				'SELECT h FROM AppBundle:Human h WHERE h.soul IS NULL AND h.deathTime IS NULL ORDER BY h.name ASC'
			)
			->getResult();
	}

	public function findAllIncarnated()
	{
		return $this->getEntityManager()
			->createQuery(
				'SELECT h FROM AppBundle:Human h WHERE h.soul IS NOT NULL ORDER BY h.name ASC'
			)
			->getResult();
	}

}