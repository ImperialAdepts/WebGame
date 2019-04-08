<?php

namespace PlanetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Human
 *
 * @ORM\Table(name="humans")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\HumanRepository")
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
     * @ORM\Column(name="global_human_id", type="bigint", nullable=true)
     */
    private $globalHumanId;

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
     * @var PlanetBundle\Entity\Settlement
     *
     * @ORM\ManyToOne(targetEntity="\PlanetBundle\Entity\Settlement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="current_settlement_id", referencedColumnName="id")
     * })
     */
    private $currentPosition;

    /**
     * @var Human
     *
     * @ORM\ManyToOne(targetEntity="Human")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="father_human_id", referencedColumnName="id")
     * })
     */
    private $fatherHuman;

    /**
     * @var Human
     *
     * @ORM\ManyToOne(targetEntity="Human")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mother_human_id", referencedColumnName="id")
     * })
     */
    private $motherHuman;


    /**
     * @ORM\Get("id")
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
    public function getGlobalHumanId()
    {
        return $this->globalHumanId;
    }

    /**
     * @param string $globalHumanId
     */
    public function setGlobalHumanId($globalHumanId)
    {
        $this->globalHumanId = $globalHumanId;
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
     * @return int
     */
    public function getBornIn()
    {
        return $this->bornIn;
    }

    /**
     * @param int $bornIn
     */
    public function setBornIn($bornIn)
    {
        $this->bornIn = $bornIn;
    }

    /**
     * @return int
     */
    public function getDiedIn()
    {
        return $this->diedIn;
    }

    /**
     * @param int $diedIn
     */
    public function setDiedIn($diedIn)
    {
        $this->diedIn = $diedIn;
    }

    /**
     * @return Settlement
     */
    public function getCurrentPosition()
    {
        return $this->currentPosition;
    }

    /**
     * @param Settlement $currentPosition
     */
    public function setCurrentPosition(Settlement $currentPosition)
    {
        $this->currentPosition = $currentPosition;
    }

    /**
     * @return int
     */
    public function getFamilyId()
    {
        return $this->familyId;
    }

    /**
     * @param int $familyId
     */
    public function setFamilyId($familyId)
    {
        $this->familyId = $familyId;
    }

    /**
     * @return Human
     */
    public function getFatherHuman()
    {
        return $this->fatherHuman;
    }

    /**
     * @param Human $fatherHuman
     */
    public function setFatherHuman($fatherHuman)
    {
        $this->fatherHuman = $fatherHuman;
    }

    /**
     * @return Human
     */
    public function getMotherHuman()
    {
        return $this->motherHuman;
    }

    /**
     * @param Human $motherHuman
     */
    public function setMotherHuman($motherHuman)
    {
        $this->motherHuman = $motherHuman;
    }

}

