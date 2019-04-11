<?php
namespace AppBundle\Repository;

use AppBundle\Entity; use PlanetBundle\Entity as PlanetEntity;

class SoulRepository extends \Doctrine\ORM\EntityRepository
{

	public function findAllOrderedByName()
	{
		return $this->getEntityManager()
			->createQuery(
				'SELECT s FROM AppBundle:Soul s ORDER BY s.name ASC'
			)
			->getResult();
	}

	public function getByGamer(Entity\Gamer $gamer)
	{
		return $this->getEntityManager()
			->createQuery(
				'SELECT s FROM AppBundle:Soul s WHERE s.gamer = :gamerId ORDER BY s.name ASC'
			)
            ->setParameter('gamerId', $gamer->getId())
			->getResult();
	}

    public function getByGamerAlignment(Entity\Gamer $gamer, $alignment)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT s FROM AppBundle:Soul s WHERE s.gamer = :gamerId AND s.alignment = :alignment ORDER BY s.name ASC'
            )
            ->setParameter('gamerId', $gamer->getId())
            ->setParameter('alignment', $alignment)
            ->getResult();
    }
}