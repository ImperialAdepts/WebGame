<?php

namespace AppBundle\Entity\SolarSystem;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlanetSatellite
 *
 * @ORM\Table(name="planet_satellites", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="planets_UN", columns={"id", "system_uuid"}),
 *     @ORM\UniqueConstraint(name="planet_satellites_UN", columns={"orbit_uuid", "orbit_position"})
 * })
 * @ORM\Entity
 */
class PlanetSatellite
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
     * @ORM\Column(name="system_uuid", type="string", nullable=false)
     */
    private $systemUuid;

    /**
     * @var integer
     *
     * @ORM\Column(name="orbit_uuid", type="string", nullable=false)
     */
    private $orbitUuid;

    /**
     * @var string
     *
     * @ORM\Column(name="orbit_position", type="string", length=255, nullable=false)
     */
    private $orbitPosition;


}

