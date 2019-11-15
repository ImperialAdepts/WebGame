<?php

namespace PlanetBundle\Builder;

use AppBundle\Descriptor\ResourcefullInterface;
use PlanetBundle\Concept\Team\Team;
use PlanetBundle\Entity;
use Doctrine\ORM\EntityManager;
use PlanetBundle\Entity\Deposit;
use Tracy\Debugger;

class RegionBuilder
{
    /** @var Entity\Resource\DepositInterface */
    private $deposit;
    /** @var Entity\Resource\BlueprintRecipe */
    private $recipe;
    /** @var Entity\Human */
    private $supervisor;
    /** @var Team|Team[]|null null => all available */
    private $workingTeams = [];
    /** @var int|null null => max possible */
    private $count = 1;

    /**
     * RegionBuilder constructor.
     * @param Entity\Resource\DepositInterface $whereToBuild
     * @param Entity\Resource\BlueprintRecipe $blueprint
     */
    public function __construct(Entity\Resource\DepositInterface $whereToBuild, Entity\Resource\BlueprintRecipe $blueprint)
    {
        $this->recipe = $blueprint;
        $this->deposit = $whereToBuild;
    }

    /**
     * @return boolean
     */
    public function isValidBuildable() {
        foreach ($this->recipe->getInputs() as $input) {
            if (!$this->deposit->contains($input)) {
                return false;
            }
        }
        foreach ($this->recipe->getTools() as $tool) {
            if (!$this->deposit->contains($tool)) {
                return false;
            }
        }
        return true;
    }

    public function getValidationErrors() {
        $errors = [];
        foreach ($this->recipe->getInputs() as $input) {
            if (!$this->deposit->contains($input)) {
                if ($input instanceof Entity\Resource\Thing) {
                    $errors['input'][] = [
                        'blueprint' => $input->getBlueprint(),
                        'missingCount' => $input->getAmount() - Deposit::sumAmounts($this->deposit->filterByBlueprint($input->getBlueprint())),
                    ];
                }
            }
        }
        foreach ($this->recipe->getTools() as $tool) {
            if (!$this->deposit->contains($tool)) {
                if ($tool instanceof Entity\Resource\Thing) {
                    $errors['tool'][] = [
                        'blueprint' => $tool->getBlueprint(),
                        'missingCount' => $tool->getAmount() - Deposit::sumAmounts($this->deposit->filterByBlueprint($tool->getBlueprint())),
                    ];
                }
            }
        }
        return $errors;
    }

    /**
     * @return int
     */
    public function getPosibilityCount() {
        if (!$this->isValidBuildable()) return 0;

        $possibleCount = null;
        foreach ($this->recipe->getInputs() as $input) {
            if ($input instanceof Entity\Resource\Thing) {
                $resourcePosibility = Deposit::sumAmounts($this->deposit->filterByBlueprint($input->getBlueprint())) / $input->getAmount();
                if ($resourcePosibility < 1) {
                    return 0;
                } elseif ($possibleCount === null) {
                    $possibleCount = $resourcePosibility;
                } elseif ($resourcePosibility < $possibleCount) {
                    $possibleCount = $resourcePosibility;
                }
            }
        }
        foreach ($this->recipe->getTools() as $tool) {
            if ($tool instanceof Entity\Resource\Thing) {
                $resourcePosibility = Deposit::sumAmounts($this->deposit->filterByBlueprint($tool->getBlueprint())) / $tool->getAmount();
                if ($resourcePosibility < 1) {
                    return 0;
                } elseif ($possibleCount === null) {
                    $possibleCount = $resourcePosibility;
                } elseif ($resourcePosibility < $possibleCount) {
                    $possibleCount = $resourcePosibility;
                }
            }
        }

        if ($possibleCount == null || $possibleCount < 1) {
            return 0;
        } else {
            return floor($possibleCount);
        }
    }

    /**
     * @return int amout of builded items
     */
    public function build() {
        $counter = 0;
        if ($this->count === null) {
            while (true) {
                if ($this->buildOne()) {
                    $counter++;
                } else {
                    return $counter;
                }
            }
        } else {
            for ($i = 0; $i < $this->count; $i++) {
                if ($this->buildOne()){
                    $counter++;
                } else {
                    return $counter;
                }
            }
        }
        return $counter;
    }

    private function buildOne() {
        if (!$this->isValidBuildable()) {
            return false;
        }

        foreach ($this->recipe->getInputs()->getResourceDescriptors() as $productedResources) {
            $this->deposit->consume($productedResources);
        }

        foreach ($this->recipe->getProducts()->getResourceDescriptors() as $productedResources) {
            $this->deposit->addResourceDescriptors($productedResources);
        }

        return true;
    }

    /**
     * @param Entity\Human $supervisor
     */
    public function setSupervisor(Entity\Human $supervisor)
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
     * @param int|null $count null => infinite
     */
    public function setCount($count)
    {
        if (!is_numeric($count) || !$count === null) {
            throw new \InvalidArgumentException("Count must be number or null");
        }
        $this->count = (int)$count;
    }


}