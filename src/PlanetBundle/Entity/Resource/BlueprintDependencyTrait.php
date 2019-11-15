<?php

namespace PlanetBundle\Entity\Resource;

use Doctrine\ORM\Mapping as ORM;

trait BlueprintDependencyTrait
{
    /**
     * @var Blueprint
     *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Resource\Blueprint")
     * @ORM\JoinColumn(name="blueprint_id", referencedColumnName="id", nullable=false)
     */
    private $blueprint;

    /**
     * @return Blueprint
     */
    public function getBlueprint()
    {
        return $this->blueprint;
    }

    /**
     * @param Blueprint $blueprint
     */
    public function setBlueprint(Blueprint $blueprint)
    {
        $this->blueprint = $blueprint;
    }

}

