<?php
namespace PlanetBundle\Concept;

use PlanetBundle\Annotation\Concept\CreationSource;
use PlanetBundle\Annotation\Concept\CreationWaste;
use PlanetBundle\Annotation\Concept\Persistent;
use PlanetBundle\Annotation\Concept\CreationDifficulty;

/**
 * @CreationWaste(minCrapAmount=5, maxCrapAmount=20)
 * @CreationDifficulty(workHourPerCubicMeter="3", workHourPerTon="10")
 * @CreationSource(Traverse::class)
 */
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