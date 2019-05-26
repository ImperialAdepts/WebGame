<?php

namespace AppBundle\Repository;

use PlanetBundle\Entity\RegionResourceDeposit;

/**
 * Class RegionResourceDepositRepository
 * @package AppBundle\Repository
 */
class RegionResourceDepositRepository extends ResourceDepositRepository
{
    /**
     * @return RegionResourceDeposit[]
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
     * @return RegionResourceDeposit[]
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
