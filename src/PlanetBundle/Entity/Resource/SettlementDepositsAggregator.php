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
        foreach ($this->getSubDeposits() as $deposit) {
            foreach ($deposit->getResourceDescriptors() as $descriptor) {
                yield $descriptor;
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
        $descriptors = [];
        foreach ($this->getSubDeposits() as $deposit) {
            foreach ($deposit->filterByUseCase($useCase) as $descriptor) {
                $descriptors[] = $descriptor;
            }
        }
        return $descriptors;
    }

    /**
     * @param Blueprint $blueprint
     * @return Thing[]
     */
    public function filterByBlueprint(Blueprint $blueprint)
    {
        $descriptors = [];
        foreach ($this->getSubDeposits() as $deposit) {
            foreach ($deposit->filterByBlueprint($blueprint) as $descriptor) {
                $descriptors[] = $descriptor;
            }
        }
        return $descriptors;
    }

    /**
     * @param string $concept
     * @return Thing[]
     */
    public function filterByConcept($concept)
    {
        $descriptors = [];
        foreach ($this->getSubDeposits() as $deposit) {
            foreach ($deposit->filterbyConcept($concept) as $descriptor) {
                $descriptors[] = $descriptor;
            }
        }
        return $descriptors;
    }

    public function getSubDeposits() {
        /** @var Region $region */
        foreach ($this->settlement->getRegions() as $region) {
            if ($region->getDeposit() != null) {
                yield $region->getDeposit();
            }
        }
        /** @var Peak $peak */
        foreach ($this->settlement->getPeaks() as $peak) {
            if ($peak->getDeposit() != null) {
                yield $peak->getDeposit();
            }
        }
    }
}