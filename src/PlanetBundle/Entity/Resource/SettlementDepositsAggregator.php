<?php
namespace PlanetBundle\Entity\Resource;

use PlanetBundle\Entity\Peak;
use PlanetBundle\Entity\PeakDeposit;
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
        $deposit = $this->settlement->getAdministrativeCenter()->getDeposit();
        if (!$deposit) {
            $deposit = new PeakDeposit();
            $deposit->setPeak($this->settlement->getAdministrativeCenter());
            $this->settlement->getAdministrativeCenter()->setDeposit($deposit);
        }
        $deposit->addResourceDescriptors($resourceDescriptor);
    }

    /**
     * @param string $useCase trait class
     * @return Thing[]
     */
    public function filterByUseCase($useCase)
    {
        /** @var Region $region */
        foreach ($this->settlement->getRegions() as $region) {
            foreach ($region->getDeposit()->filterByUseCase($useCase) as $descriptor) {
                yield $descriptor;
            }
        }
        /** @var Peak $peak */
        foreach ($this->settlement->getPeaks() as $peak) {
            if ($peak->getDeposit() != null) {
                foreach ($peak->getDeposit()->filterByUseCase($useCase) as $descriptor) {
                    yield $descriptor;
                }
            }
        }
    }

    /**
     * @param Blueprint $blueprint
     * @return Thing[]
     */
    public function filterByBlueprint(Blueprint $blueprint)
    {
        /** @var Region $region */
        foreach ($this->settlement->getRegions() as $region) {
            foreach ($region->getDeposit()->filterByBlueprint($blueprint) as $descriptor) {
                yield $descriptor;
            }
        }
        /** @var Peak $peak */
        foreach ($this->settlement->getPeaks() as $peak) {
            if ($peak->getDeposit() != null) {
                foreach ($peak->getDeposit()->filterByBlueprint($blueprint) as $descriptor) {
                    yield $descriptor;
                }
            }
        }
    }
}