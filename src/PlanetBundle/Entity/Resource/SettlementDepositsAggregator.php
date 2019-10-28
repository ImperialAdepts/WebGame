<?php
namespace PlanetBundle\Entity\Resource;

use PlanetBundle\Entity\Peak;
use PlanetBundle\Entity\Region;
use PlanetBundle\Entity\Settlement;

class SettlementDepositsAggregator implements DepositInterface
{
    /** @var Settlement */
    private $settlement;

    /**
     * SettlementDepositsAggregator constructor.
     * @param Settlement $settlement
     */
    public function __construct(Settlement $settlement)
    {
        $this->settlement = $settlement;
    }

    /**
     * @return ResourceDescriptor[]
     */
    public function getResourceDescriptors()
    {
        /** @var Region $region */
        foreach ($this->settlement->getRegions() as $region) {
            if ($region->getDeposit() != null) {
                foreach ($region->getDeposit()->getResourceDescriptors() as $descriptor) {
                    yield $descriptor;
                }
            }
        }
        /** @var Peak $peak */
        foreach ($this->settlement->getPeaks() as $peak) {
            if ($peak->getDeposit() != null) {
                foreach ($peak->getDeposit()->getResourceDescriptors() as $descriptor) {
                    yield $descriptor;
                }
            }
        }
    }

    /**
     * @param ResourceDescriptor $resourceDescriptor
     */
    public function addResourceDescriptors(ResourceDescriptor $resourceDescriptor)
    {
        $this->settlement->getAdministrativeCenter()->getDeposit()->addResourceDescriptors($resourceDescriptor);
    }
}