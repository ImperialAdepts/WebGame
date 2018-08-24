<?php

namespace AppBundle\Entity\SolarSystem;

use Doctrine\ORM\Mapping as ORM;

/**
 * SystemGivenName
 *
 * @ORM\Table(name="solar_system_names")
 * @ORM\Entity
 */
class SystemGivenName
{
    /**
     * @var integer
     *
     * @ORM\Column(name="uuid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $uuid;

    /**
     * @var integer
     *
     * @ORM\Column(name="human_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $humanId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;


}

