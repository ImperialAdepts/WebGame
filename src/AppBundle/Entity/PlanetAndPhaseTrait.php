<?php
namespace AppBundle\Entity;

use AppBundle\Entity\SolarSystem\Planet;
use Doctrine\ORM\Mapping as ORM;

trait PlanetAndPhaseTrait
{

    /**
     * @var Planet
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SolarSystem\Planet")
     * @ORM\JoinColumn(name="planet_id", referencedColumnName="id", nullable=false)
     */
    private $planet;

    /**
     * @var integer
     *
     * @ORM\Column(name="planet_phase", type="integer", nullable=false)
     */
    private $planetPhase;

    /**
     * @return Planet
     */
    public function getPlanet()
    {
        return $this->planet;
    }

    /**
     * @param Planet $planet
     */
    public function setPlanet(Planet $planet)
    {
        $this->planet = $planet;
    }

    /**
     * @return int
     */
    public function getPlanetPhase()
    {
        return $this->planetPhase;
    }

    /**
     * @param int $planetPhase
     */
    public function setPlanetPhase($planetPhase)
    {
        $this->planetPhase = $planetPhase;
    }
}