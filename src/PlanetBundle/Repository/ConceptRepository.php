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
        ];
    }

    /**
     * @param $useCase
     * @return string
     */
    public function getByUseCase($useCase)
    {
        $concepts = [];

        foreach ($this->getAll() as $concept) {
            $uses = class_uses($concept);

            foreach (class_parents($concept) as $class_parent) {
                $uses = array_merge($uses, class_uses($class_parent));
            }
//            echo "$concept<br>/n";
//            Debugger::dump($uses);
            if (in_array($useCase, $uses)) {
                $concepts[] = $concept;
            }
        }

//        Debugger::dump($useCase);
//        Debugger::dump($concepts);
//        die;

        return $concepts;
    }

}
