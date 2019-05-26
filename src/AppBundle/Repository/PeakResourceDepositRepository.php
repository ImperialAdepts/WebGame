<?php

namespace AppBundle\Repository;

use PlanetBundle\Entity\PeakResourceDeposit;

/**
 * Class PeakResourceDepositRepository
 * @package AppBundle\Repository
 */
class PeakResourceDepositRepository extends ResourceDepositRepository
{
    /**
     * @return PeakResourceDeposit[]
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
     * @return PeakResourceDeposit[]
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
