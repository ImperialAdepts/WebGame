<?php
namespace PlanetBundle\Concept;

abstract class Concept
{
    public static function getParts() {
        return [];
    }

    public function injectChangeableData(array $data) {

    }
}