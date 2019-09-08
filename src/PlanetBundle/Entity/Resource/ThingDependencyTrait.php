<?php

namespace PlanetBundle\Entity\Resource;

use Doctrine\ORM\Mapping as ORM;

trait ThingDependencyTrait
{
    /**
     * @var Thing
     *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Resource\Thing")
     * @ORM\JoinColumn(name="resource_descriptior_id", referencedColumnName="id", nullable=true)
     */
    private $thing;

    /**
     * @return ResourceDescriptor
     */
    public function getThing()
    {
        return $this->thing;
    }

    /**
     * @param Thing $thing
     */
    public function setThing(Thing $thing)
    {
        $this->thing = $thing;
    }

}

