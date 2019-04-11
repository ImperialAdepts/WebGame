<?php

namespace AppBundle\Entity;

use AppBundle\EnumAlignmentType;
use Doctrine\ORM\Mapping as ORM;

/**
 * Soul
 *
 * @ORM\Table(name="souls", indexes={@ORM\Index(name="souls_gamers_FK", columns={"gamer_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SoulRepository")
 */
class Soul
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var Gamer
     *
     * @ORM\ManyToOne(targetEntity="Gamer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gamer_id", referencedColumnName="id")
     * })
     */
    private $gamer;

    /**
     * @var Human
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Human", mappedBy="soul")
     */
    private $incarnations;

    /**
     * @ORM\Column(type="alignment_enum")
     */
    private $alignment = EnumAlignmentType::NEUTRAL_NEUTRAL;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return Gamer
     */
    public function getGamer()
    {
        return $this->gamer;
    }

    /**
     * @param Gamer $gamer
     */
    public function setGamer($gamer)
    {
        $this->gamer = $gamer;
    }

    /**
     * @return Human
     */
    public function getIncarnations()
    {
        return $this->incarnations;
    }

    /**
     * @param Human $incarnations
     */
    public function setIncarnations($incarnations)
    {
        $this->incarnations = $incarnations;
    }

    /**
     * @return string
     */
    public function getAlignment()
    {
        return $this->alignment;
    }

    /**
     * @param string $alignment
     */
    public function setAlignment($alignment)
    {
        $this->alignment = $alignment;
    }

    /**
     * @return boolean
     */
    public function isMoralGood() {
        return in_array($this->getAlignment(), [
            EnumAlignmentType::CHAOTIC_GOOD,
            EnumAlignmentType::LAWFUL_GOOD,
            EnumAlignmentType::NEUTRAL_GOOD,
        ]);
    }

    /**
     * @return boolean
     */
    public function isMoralNeutral() {
        return in_array($this->getAlignment(), [
            EnumAlignmentType::NEUTRAL_NEUTRAL,
            EnumAlignmentType::LAWFUL_NEUTRAL,
            EnumAlignmentType::CHAOTIC_NEUTRAL,
        ]);
    }

    /**
     * @return boolean
     */
    public function isMoralEvil() {
        return in_array($this->getAlignment(), [
            EnumAlignmentType::CHAOTIC_EVIL,
            EnumAlignmentType::LAWFUL_EVIL,
            EnumAlignmentType::NEUTRAL_EVIL,
        ]);
    }

    /**
     * @return boolean
     */
    public function isOrderLawful() {
        return in_array($this->getAlignment(), [
            EnumAlignmentType::LAWFUL_EVIL,
            EnumAlignmentType::LAWFUL_NEUTRAL,
            EnumAlignmentType::LAWFUL_GOOD,
        ]);
    }

    /**
     * @return boolean
     */
    public function isOrderNeutral() {
        return in_array($this->getAlignment(), [
            EnumAlignmentType::NEUTRAL_EVIL,
            EnumAlignmentType::NEUTRAL_GOOD,
            EnumAlignmentType::NEUTRAL_NEUTRAL,
        ]);
    }

    /**
     * @return boolean
     */
    public function isOrderChaotic() {
        return in_array($this->getAlignment(), [
            EnumAlignmentType::CHAOTIC_EVIL,
            EnumAlignmentType::CHAOTIC_GOOD,
            EnumAlignmentType::CHAOTIC_NEUTRAL,
        ]);
    }
}

