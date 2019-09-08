<?php

namespace AppBundle\Repository;

use PlanetBundle\Entity\PeakDeposit;

/**
 * Class PeakResourceDepositRepository
 * @package AppBundle\Repository
 */
class PeakResourceDepositRepository extends ResourceDepositRepository
{
    /**
     * @return PeakDeposit[]
     */
    public function getAll()
    {
        return $this->getEntityManager()
            ->createQuery(
                "SELECT rd FROM PlanetBundle:PeakResourceDeposit rd"
            )
            ->getResult();
    }

    /**
     * @return PeakDeposit[]
     */
    public function getEmpty()
    {
        return $this->getEntityManager()
            ->createQuery(
                "SELECT rd FROM PlanetBundle:PeakResourceDeposit rd where rd.amount = 0"
            )
            ->getResult();
    }
}
