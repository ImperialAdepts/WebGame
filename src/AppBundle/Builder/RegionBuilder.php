<?php

namespace AppBundle\Builder;

use AppBundle\Descriptor\Adapters\AbstractResourceDepositAdapter;
use AppBundle\Descriptor\Adapters\Team;
use AppBundle\Entity\Blueprint;
use AppBundle\Entity\Human;
use AppBundle\Entity\Planet\Region;
use Doctrine\ORM\EntityManager;

class RegionBuilder
{
    /** @var EntityManager */
    private $entityManager;

    /** @var Blueprint */
    private $blueprint;
    /** @var Human */
    private $supervisor;
    /** @var Team|Team[]|null null => all available */
    private $workingTeams = [];
    /** @var Region */
    private $region;
    /** @var int|null null => max possible */
    private $count = 1;

    /**
     * RegionBuilder constructor.
     * @param EntityManager $entityManager
     * @param Blueprint $blueprint
     */
    public function __construct(EntityManager $entityManager, Blueprint $blueprint)
    {
        $this->entityManager = $entityManager;
        $this->blueprint = $blueprint;
    }


    /**
     * @return boolean
     */
    public function isValidBuildable() {
        foreach ($this->blueprint->getResourceRequirements() as $resourceDescriptor => $count) {
            if ($this->region->getResourceDeposit($resourceDescriptor) === null) {
                return false;
            }
            if ($this->region->getResourceDeposit($resourceDescriptor)->getAmount() < $count) {
                return false;
            }
        }
        foreach ($this->blueprint->getUseCaseRequirements() as $useCaseName => $traits) {
            /** @var AbstractResourceDepositAdapter[] $adapters */
            $adapters = AbstractResourceDepositAdapter::extractAdapterOfUseCase($this->region, $useCaseName);
            $isAnyGoodAdapter = false;
            /** @var AbstractResourceDepositAdapter $adapter */
            foreach ($adapters as $adapter) {
                $isAdapterGood = true;
                foreach ($traits as $traitName => $requiredTraitValue) {
                    $adapterTraitValue = $adapter->getBlueprint()->getTraitValue($traitName, 0  );
                    if (/*!is_numeric($traitValue) ||*/ $adapterTraitValue == null) {
                        $isAdapterGood = false;
                        continue;
                    }
                    if (/*is_numeric($adapterTraitValue) &&*/ $adapterTraitValue < $requiredTraitValue) {
                        $isAdapterGood = false;
                        continue;
                    }
                }
                if ($isAdapterGood) {
                    $isAnyGoodAdapter = true;
                    break;
                }
            }
            if (!$isAnyGoodAdapter) return false;
        }
        return true;
    }

    public function getValidationErrors() {
        $missingResources = [];
        foreach ($this->blueprint->getResourceRequirements() as $resourceDescriptor => $count) {
            if ($this->region->getResourceDeposit($resourceDescriptor) === null) {
                $missingResources[$resourceDescriptor] = $count;
                continue;
            }
            if ($this->region->getResourceDeposit($resourceDescriptor)->getAmount() < $count) {
                $missingResources[$resourceDescriptor] = $this->region->getResourceDeposit($resourceDescriptor)->getAmount() - $count;
            }
        }
        $missingUseCases = [];
        $wrongTraits = [];
        foreach ($this->blueprint->getUseCaseRequirements() as $useCaseName => $traits) {
            /** @var AbstractResourceDepositAdapter[] $adapters */
            $adapters = AbstractResourceDepositAdapter::extractAdapterOfUseCase($this->region, $useCaseName);
            if (count($adapters) == 0) {
                $missingUseCases[] = $useCaseName;
                continue;
            }
            $hasAnySuitableAdapter = false;
            /** @var AbstractResourceDepositAdapter $adapter */
            foreach ($adapters as $adapter) {
                foreach ($traits as $traitName => $traitValue) {
                    $wrongTraits['neededValue'][$useCaseName.'|'.$traitName] = $traitValue;
                    $adapterTraitValue = $adapter->getBlueprint()->getTraitValue($traitName);
                    if (!is_numeric($traitValue)) {
                        $wrongTraits['candidates'][$adapter->getBlueprint()->getId()][$useCaseName.'|'.$traitName] = "NOT_NUMBER:".$traitValue;
                        continue;
                    }
                    if (is_numeric($adapterTraitValue) && $adapterTraitValue < $traitValue) {
                        $wrongTraits['candidates'][$adapter->getBlueprint()->getId()][$useCaseName.'|'.$traitName] = $adapterTraitValue;
                        continue;
                    }
                    $wrongTraits['candidates'][$adapter->getBlueprint()->getId()][$useCaseName.'|'.$traitName] = "OK";
                }
                $hasAnySuitableAdapter = true;
            }
            if (!$hasAnySuitableAdapter) {
                $missingUseCases[] = $useCaseName;
            }
        }
        return [
            'missingResources' => $missingResources,
            'missingUseCases' => $missingUseCases,
            'wrongTraits' => $wrongTraits,
        ];
    }

    /**
     * @return int
     */
    public function getPosibilityCount() {
        if (!$this->isValidBuildable()) return 0;
        return 1;
        $count = null;
        foreach ($this->blueprint->getResourceRequirements() as $resourceDescriptor => $count) {
            $resourcePosibility = $this->region->getResourceDeposit($resourceDescriptor)->getAmount() / $count;
            if ($count === null) {
                $count = $resourcePosibility;
            } elseif ($count > $resourcePosibility) {
                $count = $resourcePosibility;
            }
        }
        if ($count == null || $count < 1) {
            return 0;
        } else {
            return floor($count);
        }
    }

    public function build() {
        $this->entityManager->beginTransaction();
        $this->entityManager->refresh($this->region);
        $this->entityManager->refresh($this->blueprint);

        foreach (range(1, $this->count?: 100000000000000000000000) as $index) {
            if (!$this->buildOne()) {
                break;
            }
        }

        $this->entityManager->flush($this->region);
        $this->entityManager->flush($this->blueprint);
        $this->entityManager->commit();
    }

    private function buildOne() {
        if (!$this->isValidBuildable()) {
            return false;
        }

        foreach ($this->blueprint->getResourceRequirements() as $resourceDescriptor => $requirementCount) {
            $currentAmount = $this->region->getResourceDeposit($resourceDescriptor)->getAmount();
            $this->region->getResourceDeposit($resourceDescriptor)->setAmount($currentAmount - $requirementCount);
        }

        $this->region->addResourceDeposit($this->blueprint);
        return true;
    }

    /**
     * @param Human $supervisor
     */
    public function setSupervisor(Human $supervisor)
    {
        $this->supervisor = $supervisor;
    }

    /**
     * @param Team[] $workingTeams
     */
    public function setWorkingTeams(array $workingTeams)
    {
        $this->workingTeams = $workingTeams;
    }

    public function setAllRegionTeams()
    {
        $this->workingTeams = null;
    }

    /**
     * @param Team $workingTeam
     */
    public function addWorkingTeam($workingTeam)
    {
        $this->workingTeams[] = $workingTeam;
    }

    /**
     * @param Region $region
     */
    public function setRegion(Region $region)
    {
        $this->region = $region;
    }

    /**
     * @param int|null $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }


}