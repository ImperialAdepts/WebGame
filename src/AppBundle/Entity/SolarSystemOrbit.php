<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SolarSystemOrbit
 *
 * @ORM\Table(name="solar_system_orbits", uniqueConstraints={@ORM\UniqueConstraint(name="solar_system_orbit_position_UN", columns={"radius", "offset", "orbiting_planet_uuid"})}, indexes={@ORM\Index(name="solar_system_orbits_solar_systems_FK", columns={"system_uuid"}), @ORM\Index(name="solar_system_orbits_planets_FK", columns={"orbiting_planet_uuid"})})
 * @ORM\Entity
 */
class SolarSystemOrbit
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="system_uuid", type="bigint", nullable=false)
     */
    private $systemUuid;

    /**
     * @var integer
     *
     * @ORM\Column(name="orbiting_planet_uuid", type="bigint", nullable=false)
     */
    private $orbitingPlanetUuid;

    /**
     * @var integer
     *
     * @ORM\Column(name="radius", type="integer", nullable=false)
     */
    private $radius;

    /**
     * @var integer
     *
     * @ORM\Column(name="offset", type="integer", nullable=false)
     */
    private $offset;


}

