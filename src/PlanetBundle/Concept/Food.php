<?php
namespace PlanetBundle\Concept;

use PlanetBundle\Entity\Resource\Thing;
use PlanetBundle\UseCase;

class Food extends Concept
{
    use UseCase\Portable;
    use UseCase\Consumable;

    /**
     * @param Food[] $foods
     * @return float|int
     */
    public static function countVariety($foods) {
        if (count($foods) == 0) return 0;
        $average = 0;
        $average = $average / count($foods);
        $varietyFactor = 0;
        /** @var Thing $foodThing */
        foreach ($foods as $foodThing) {
            if ($foodThing->getAmount() > $average) {
                $varietyFactor += 1;
            } else {
                $varietyFactor += ($average / $foodThing->getAmount());
            }
        }
        return $varietyFactor;
    }
}