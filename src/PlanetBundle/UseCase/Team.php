<?php


namespace PlanetBundle\UseCase;


use PlanetBundle\Annotation\Concept\Persistent;

trait Team
{
    /**
     * @var integer
     * @Persistent()
     */
    private $peopleCount;

    /**
     * @return integer
     */
    public function getPeopleCount()
    {
        return $this->peopleCount;
    }

    /**
     * @param integer $peopleCount
     */
    public function setPeopleCount($peopleCount)
    {
        $this->peopleCount = $peopleCount;
    }


}