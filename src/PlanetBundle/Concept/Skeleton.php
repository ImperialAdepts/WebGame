<?php
namespace PlanetBundle\Concept;

use PlanetBundle\Annotation\Concept\Persistent;

class Skeleton extends Concept
{
    /**
     * @var string
     * @Persistent()
     */
    private $material;

    /**
     * @var string
     * @Persistent()
     */
    private $robust;

    /**
     * @return string
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * @param string $material
     */
    public function setMaterial($material)
    {
        $this->material = $material;
    }

    /**
     * @return string
     */
    public function getRobust()
    {
        return $this->robust;
    }

    /**
     * @param string $robust
     */
    public function setRobust($robust)
    {
        $this->robust = $robust;
    }
}