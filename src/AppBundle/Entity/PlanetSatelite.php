<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlanetSatelite
 *
 * @ORM\Table(name="planet_satelites", uniqueConstraints={@ORM\UniqueConstraint(name="planets_UN", columns={"id", "system_uuid"}), @ORM\UniqueConstraint(name="planet_satelites_UN", columns={"orbit_id", "orbit_position"})})
 * @ORM\Entity
 */
class PlanetSatelite
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
     * @ORM\Column(name="orbit_id", type="bigint", nullable=false)
     */
    private $orbitId;

    /**
     * @var string
     *
     * @ORM\Column(name="orbit_position", type="string", length=255, nullable=false)
     */
    private $orbitPosition;


}

