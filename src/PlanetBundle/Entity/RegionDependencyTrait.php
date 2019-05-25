<?php
namespace PlanetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait RegionDependencyTrait
{
    /**
     * @var Region
     *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Region")
     * @ORM\JoinColumns(
     *  @ORM\JoinColumn(name="region_peak_center_id", referencedColumnName="peak_center_id", nullable=false),
     *  @ORM\JoinColumn(name="region_peak_left_id", referencedColumnName="peak_left_id", nullable=false),
     *  @ORM\JoinColumn(name="region_peak_right_id", referencedColumnName="peak_right_id", nullable=false)
     * )
     */
    private $region;

    /**
     * Set settlement
     *
     * @param Region $region
     */
    public function setRegion(Region $region)
    {
        $this->region = $region;
    }

    /**
     * Get settlement
     *
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }
}