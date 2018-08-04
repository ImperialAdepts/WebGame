<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Human
 *
 * @ORM\Table(name="humans", indexes={@ORM\Index(name="humans_souls_FK", columns={"soul_id"}), @ORM\Index(name="humans_humans_FK", columns={"father_human_id"}), @ORM\Index(name="humans_humans_mother_FK", columns={"mother_human_id"})})
 * @ORM\Entity
 */
class Human
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="born_in", type="bigint", nullable=false)
     */
    private $bornIn;

    /**
     * @var integer
     *
     * @ORM\Column(name="died_in", type="bigint", nullable=true)
     */
    private $diedIn;

    /**
     * @var integer
     *
     * @ORM\Column(name="family_id", type="bigint", nullable=true)
     */
    private $familyId;

    /**
     * @var Humans
     *
     * @ORM\ManyToOne(targetEntity="Humans")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="father_human_id", referencedColumnName="id")
     * })
     */
    private $fatherHuman;

    /**
     * @var Humans
     *
     * @ORM\ManyToOne(targetEntity="Humans")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mother_human_id", referencedColumnName="id")
     * })
     */
    private $motherHuman;

    /**
     * @var Souls
     *
     * @ORM\ManyToOne(targetEntity="Souls")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="soul_id", referencedColumnName="id")
     * })
     */
    private $soul;


}

