<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GlobalValue
 *
 * @ORM\Table(name="global_values")
 * @ORM\Entity
 */
class GlobalValue
{
    /**
     * @var string
     *
     * @ORM\Column(name="key", type="string", length=100, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $key;

    /**
     * @var integer
     *
     * @ORM\Column(name="value", type="bigint", nullable=false)
     */
    private $value;


}

