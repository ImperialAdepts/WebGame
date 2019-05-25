<?php

namespace PlanetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait PeakDependencyTrait
{
    /**
     * @var Peak
     *
     * @ORM\ManyToOne(targetEntity="Peak")
     * @ORM\JoinColumn(name="peak_id", referencedColumnName="id")
     */
    private $peak;

    /**
     * @return Peak
     */
    public function getPeak()
    {
        return $this->peak;
    }

    /**
     * @param Peak $peak
     */
    public function setPeak($peak)
    {
        $this->peak = $peak;
    }

}