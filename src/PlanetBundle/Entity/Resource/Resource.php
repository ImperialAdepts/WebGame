<?php

namespace PlanetBundle\Entity\Resource;

use Doctrine\ORM\Mapping as ORM;

/**
 * Resource
 *
 * @ORM\Entity()
 * @ORM\Table(name="resource_descriptor_resources")
 */
class Resource extends ResourceDescriptor
{

	/**
	 * @var string
	 *
	 * @ORM\Column(name="type", type="string", length=255)
	 */
	private $type;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

}

