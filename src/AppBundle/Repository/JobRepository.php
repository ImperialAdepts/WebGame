<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Job\BuildJob;
use AppBundle\Entity\Job\BuyJob;
use AppBundle\Entity\Job\ProduceJob;
use AppBundle\Entity\Job\SellJob;
use AppBundle\Entity\Job\TransportJob;
use AppBundle\Entity\Planet\Region;
use AppBundle\Entity\Planet\Settlement;

/**
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class JobRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param Settlement $settlement
     * @return BuildJob[]
     */
    public function getBuildBySettlement(Settlement $settlement)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('job')
            ->from('AppBundle:Job\BuildJob', 'job')
            ->innerJoin('job.region', 'r')
            ->where('r.settlement=?1')
            ->setParameter(1, $settlement)
            ->orderBy('job.priority', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Settlement $settlement
     * @return TransportJob[]
     */
    public function getTransportBySettlement(Settlement $settlement)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('job')
            ->from('AppBundle:Job\TransportJob', 'job')
            ->innerJoin('job.region', 'r')
            ->where('r.settlement=?1')
            ->setParameter(1, $settlement)
            ->orderBy('job.priority', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Settlement $settlement
     * @return ProduceJob[]
     */
    public function getProduceBySettlement(Settlement $settlement)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('job')
            ->from('AppBundle:Job\ProduceJob', 'job')
            ->innerJoin('job.region', 'r')
            ->where('r.settlement=?1')
            ->setParameter(1, $settlement)
            ->orderBy('job.priority', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Settlement $settlement
     * @return BuyJob[]
     */
    public function getBuyBySettlement(Settlement $settlement)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('job')
            ->from('AppBundle:Job\BuyJob', 'job')
            ->innerJoin('job.region', 'r')
            ->where('r.settlement=?1')
            ->setParameter(1, $settlement)
            ->orderBy('job.priority', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Settlement $settlement
     * @return SellJob[]
     */
    public function getSellBySettlement(Settlement $settlement)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('job')
            ->from('AppBundle:Job\SellJob', 'job')
            ->innerJoin('job.region', 'r')
            ->where('r.settlement=?1')
            ->setParameter(1, $settlement)
            ->orderBy('job.priority', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Region $region
     * @return BuildJob[]
     */
	public function getBuildByRegion(Region $region)
	{
		return $this->getEntityManager()
			->createQuery(
				"SELECT b FROM AppBundle:Planet\BuildJob b WHERE b.region=$region ORDER BY b.priority DESC"
			)
			->getResult();
	}

}
