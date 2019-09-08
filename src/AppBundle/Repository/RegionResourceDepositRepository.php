<?php

namespace AppBundle\Repository;

use PlanetBundle\Entity\RegionDeposit;

/**
 * Class RegionResourceDepositRepository
 * @package AppBundle\Repository
 */
class RegionResourceDepositRepository extends ResourceDepositRepository
{
    /**
     * @return RegionDeposit[]
     */
    public function getAll()
    {
        return $this->getEntityManager()
            ->createQuery(
                "SELECT rd FROM PlanetBundle:RegionResourceDeposit rd"
            )
            ->getResult();
    }

    /**
     * @return RegionDeposit[]
     */
    public function getEmpty()
    {
        return $this->getEntityManager()
            ->createQuery(
                "SELECT rd FROM PlanetBundle:RegionResourceDeposit rd where rd.amount = 0"
            )
            ->getResult();
    }
}
