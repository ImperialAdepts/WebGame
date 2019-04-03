<?php

namespace AppBundle\Builder;

use AppBundle\Descriptor\Adapters\AbstractResourceDepositAdapter;
use AppBundle\Descriptor\Adapters\Team;
use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Descriptor\UseCaseTraitEnum;
use AppBundle\Entity\Blueprint;
use AppBundle\Entity\Human;
use AppBundle\Entity\Planet\Region;
use Doctrine\ORM\EntityManager;
use Tracy\Debugger;

class RegionBuilder
{
    /** @var Blueprint */
    private $blueprint;
    /** @var string[] resource_descriptor => amount */
    private $resourceRequirements;
    /** @var string[] use_case_name => [resource_descriptor => amount] */
    private $useCaseRequirements;
    /** @var Human */
    private $supervisor;
    /** @var Team|Team[]|null null => all available */
    private $workingTeams = [];
    /** @var ResourcefullInterface */
    private $resourceHolder;
    /** @var int|null null => max possible */
    private $count = 1;

    /**
     * RegionBuilder constructor.
     * @param Blueprint $blueprint
     * @param string[] $resourceRequirements
     * @param string[] $useCaseRequirements
     */
    public function __construct(Blueprint $blueprint, array $resourceRequirements, array $useCaseRequirements)
    {
        $this->blueprint = $blueprint;
        $this->resourceRequirements = $resourceRequirements;
        $this->useCaseRequirements = $useCaseRequirements;
    }

    /**
     * @return boolean
     */
    public function isValidBuildable() {
        foreach ($this->resourceRequirements as $resourceDescriptor => $count) {
            if ($this->resourceHolder->getResourceDepositAmount($resourceDescriptor) < $count) {
                return false;
            }
        }
        foreach ($this->useCaseRequirements as $useCaseName => $traits) {
            /** @var AbstractResourceDepositAdapter[] $adapters */
            $adapters = AbstractResourceDepositAdapter::extractAdapterOfUseCase($this->resourceHolder, $useCaseName);
            $isAnyGoodAdapter = false;
            /** @var AbstractResourceDepositAdapter $adapter */
            foreach ($adapters as $adapter) {
                $isAdapterGood = true;
                foreach ($traits as $traitName => $requiredTraitValue) {
                    echo " - trait: $traitName ";
                    if ($traitName == 'manpower') {
                        continue;
                    }
                    $adapterTraitValue = $adapter->getBlueprint()->getTraitValue($traitName, 0);
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
        foreach ($this->resourceRequirements as $resourceDescriptor => $requirementCount) {
            $currentCount = $this->resourceHolder->getResourceDepositAmount($resourceDescriptor);
            if ($currentCount < $requirementCount) {
                $missingResources[$resourceDescriptor] = $currentCount - $requirementCount;
            }
        }
        $missingUseCases = [];
        $wrongTraits = [];
        foreach ($this->useCaseRequirements as $useCaseName => $traits) {
            /** @var AbstractResourceDepositAdapter[] $adapters */
            $adapters = AbstractResourceDepositAdapter::extractAdapterOfUseCase($this->resourceHolder, $useCaseName);
            if (count($adapters) == 0) {
                $missingUseCases[] = $useCaseName;
                continue;
            }
            $hasAnySuitableAdapter = false;
            /** @var AbstractResourceDepositAdapter $adapter */
            foreach ($adapters as $adapter) {
                foreach ($traits as $traitName => $traitValue) {
                    if ($traitName == 'manpower') continue;
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
        $possibleCount = null;
        foreach ($this->resourceRequirements as $resourceDescriptor => $count) {
            $resourcePosibility = $this->resourceHolder->getResourceDepositAmount($resourceDescriptor) / $count;
            if ($possibleCount === null) {
                $possibleCount = $resourcePosibility;
            } elseif ($count > $resourcePosibility) {
                $possibleCount = $resourcePosibility;
            }
        }
        if ($possibleCount == null || $possibleCount < 1) {
            return 0;
        } else {
            return floor($possibleCount);
        }
    }

    public function build() {
        if ($this->count === null) {
            while (true) {
                if (!$this->buildOne()) {
                    return true;
                }
            }
        } else {
            for ($i = 1; $i < $this->count; $i++) {
                $this->buildOne();
            }
        }
    }

    private function buildOne() {
        if (!$this->isValidBuildable()) {
            return false;
        }

        foreach ($this->resourceRequirements as $resourceDescriptor => $requirementCount) {
            $currentAmount = $this->resourceHolder->getResourceDepositAmount($resourceDescriptor);
            $this->resourceHolder->getResourceDeposit($resourceDescriptor)->setAmount($currentAmount - $requirementCount);
        }

        $this->resourceHolder->addResourceDeposit($this->blueprint, 1);
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
     * @param Region $resourceHolder
     */
    public function setResourceHolder(Region $resourceHolder)
    {
        $this->resourceHolder = $resourceHolder;
    }

    /**
     * @param int|null $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }


}