<?php

namespace PlanetBundle\Repository;

use PlanetBundle\Concept;
use Tracy\Debugger;

class ConceptRepository
{

    public function getAll()
    {
        return [
            Concept\Battleship::class,
            Concept\Reactor::class,
            Concept\SpaceShip::class,
            Concept\Food::class,
            Concept\Warehouse::class,
            Concept\BurnerGenerator::class,
            Concept\FuelTank::class,
            Concept\People::class,
        ];
    }

    /**
     * @param $useCase
     * @return string
     */
    public function getByUseCase($useCase)
    {
        $concepts = [];
        $useCase = "PlanetBundle\\UseCase\\".$useCase;

        foreach ($this->getAll() as $concept) {
            $uses = class_uses($concept);

            foreach (class_parents($concept) as $class_parent) {
                $uses = array_merge($uses, class_uses($class_parent));
            }

            if (in_array($useCase, $uses)) {
                $concepts[] = $concept;
            }
        }

        return $concepts;
    }

}
