<?php
namespace PlanetBundle\Repository;

use AppBundle\Entity;
use PlanetBundle\Entity as PlanetEntity;

class HumanRepository extends \Doctrine\ORM\EntityRepository
{

	public function findByAvailableChildren()
	{
		return $this->getEntityManager()
			->createQuery(
				'SELECT h FROM PlanetBundle:Human h WHERE h.globalHumanId IS NULL ORDER BY h.id ASC'
			)
			->getResult();
	}

	public function findAllIncarnated()
	{
		return $this->getEntityManager()
			->createQuery(
				'SELECT h FROM PlanetBundle:Human h WHERE h.globalHumanId IS NOT NULL ORDER BY h.id ASC'
			)
			->getResult();
	}

    /**
     * @param Entity\Human $globalHuman
     * @return PlanetEntity\Human|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getByGlobalHuman(Entity\Human $globalHuman)
    {
        return $this->getEntityManager()
            ->createQuery(
                "SELECT h FROM PlanetBundle:Human h WHERE h.globalHumanId = :globalHuman ORDER BY h.id ASC"
            )
            ->setParameter('globalHuman', $globalHuman->getId())
            ->getOneOrNullResult();
    }
}