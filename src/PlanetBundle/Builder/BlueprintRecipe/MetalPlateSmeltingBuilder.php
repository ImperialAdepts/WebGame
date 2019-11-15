<?php

namespace PlanetBundle\Builder\BlueprintRecipe;

use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Entity\Resource\Blueprint;
use PlanetBundle\Entity\Resource\BlueprintRecipe;
use PlanetBundle\UseCase\TeamWorkers;

class MetalPlateSmeltingBuilder
{
    /**
     * @var Blueprint
     */
    private $plateBlueprint;

    private $workers = [];
    private $ores = [];

    /**
     * MetalPlateSmeltingBuilder constructor.
     * @param Blueprint $plateBlueprint
     */
    public function __construct(Blueprint $plateBlueprint)
    {
        $this->plateBlueprint = $plateBlueprint;
    }

    /**
     * @param TeamWorkers $workers
     * @param int $count default 1
     */
    public function addWorkers(TeamWorkers $workers, $count = 1) {
        $this->workers[] = [
            'blueprint' => $workers,
            'count' => $count,
        ];
    }

    /**
     * @return BlueprintRecipe
     */
    public function build() {
        $recipe = new BlueprintRecipe();
        $recipe->addTool($this->workers);
        foreach ($this->ores as $ore) {
            $recipe->addInputBlueprint($ore['blueprint'], $ore['count']);
        }
        return $recipe;
    }
}

