<?php

namespace PlanetBundle\Repository;

use PlanetBundle\Entity\Peak;
use PlanetBundle\Entity\Region;

/**
 * RegionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RegionRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * @param Region $region
     * @return Region[]
     */
	public function getRegionNeighbourhood(Region $region)
	{
	    return $this->findAll();
	    $c = $region->getPeakCenter()->getId();
	    $r = $region->getPeakRight()->getId();
	    $l = $region->getPeakLeft()->getId();
        return $this->getEntityManager()
//            ->createQuery(
//                "SELECT r FROM AppBundle:Planet\Region r WHERE "
//                . "(r.peakCenter=$c AND r.peakLeft=$l AND r.peakRight!=$r) OR "
//                . "(r.peakCenter=$c AND r.peakLeft!=$l AND r.peakRight=$r) OR "
//                . "(r.peakCenter!=$c AND r.peakLeft=$l AND r.peakRight=$r)"
//            )
            ->createQuery(
                "SELECT r FROM AppBundle:Planet\Region r, AppBundle:Planet\Region c WHERE "
                . "c.peakCenter=$c AND c.peakLeft=$l AND c.peakRight=$r AND ("
                . "(r.peakCenter=c.peakLeft AND r.peakRight=c.peakCenter) OR "
                . "(r.peakLeft=c.peakLeft AND r.peakRight=c.peakRight) OR "
                . "(r.peakCenter=c.peakRight AND r.peakLeft=c.peakCenter) "
                . ")"
            )
            ->getResult();
	}

	public function findByPeaks(Peak $regionC, Peak $regionL, Peak $regionR) {
        return $this->findOneBy([
            'peakCenter' => $regionC,
            'peakLeft' => $regionL,
            'peakRight' => $regionR]);
    }

}