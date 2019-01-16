<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\UuidSerializer;

/**
 * SolarSystem
 *
 * @ORM\Table(name="solar_systems")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SolarSystemRepository")
 */
class SolarSystem
{
    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=15)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $uuid;

    /** @var UuidSerializer\System */
    private $UUID;

    /**
     * SolarSystem constructor.
     * @param string $uuid
     */
    public function __construct($uuid)
    {
        $this->uuid = $uuid;
        $this->UUID = new UuidSerializer\System($uuid);
    }

    public function getId()
    {
        return $this->uuid;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    public function getGalaxyUuid() {
        return $this->UUID->getGalaxyUuid();
    }
}

