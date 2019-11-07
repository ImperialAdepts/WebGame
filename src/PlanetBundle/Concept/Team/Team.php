<?php
namespace PlanetBundle\Concept\Team;

use PlanetBundle\Annotation\Concept\Persistent;
use PlanetBundle\Concept\Concept;

class Team extends Concept
{
    /**
     * @var integer
     * @Persistent("integer")
     */
    private $peopleCount;

    /**
     * @return int
     */
    public function getPeopleCount()
    {
        return $this->peopleCount;
    }

    /**
     * @param int $peopleCount
     */
    public function setPeopleCount($peopleCount)
    {
        $this->peopleCount = $peopleCount;
    }
}