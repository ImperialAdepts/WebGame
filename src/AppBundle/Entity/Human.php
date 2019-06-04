<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Human\Feelings;
use AppBundle\Entity\rpg\HumanPreference;
use AppBundle\Entity\rpg\Knowledge;
use AppBundle\Entity\SolarSystem\Planet;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Human - pointer to user in another database
 *
 * @ORM\Table(name="humans")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HumanRepository")
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
     * @ORM\Column(name="hours", type="smallint", nullable=false)
     */
    private $hours = 0;

    /**
     * @var Soul
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Soul", inversedBy="incarnations")
     * @ORM\JoinColumn(name="soul_id", referencedColumnName="id", nullable=true)
     */
    private $soul;

    /**
     * @var Planet
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SolarSystem\Planet")
     * @ORM\JoinColumn(name="planet_id", referencedColumnName="id", nullable=true)
     */
    private $planet;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\rpg\HumanPreference", mappedBy="human", cascade={"all"})
     */
    private $preferences;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\rpg\Knowledge", mappedBy="human", cascade={"all"})
     */
    private $knowledge;

    /**
     * @var Feelings
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Human\Feelings", mappedBy="human", cascade={"all"})
     */
    private $feelings;

    /**
     * @var integer
     *
     * @ORM\Column(name="born_phase", type="integer", nullable=false)
     */
    private $bornPhase;

    /**
     * @var integer
     *
     * @ORM\Column(name="died_in", type="integer", nullable=true)
     */
    private $deathTime;

    /**
     * @var Planet
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SolarSystem\Planet")
     * @ORM\JoinColumn(name="planet_born_id", referencedColumnName="id", nullable=false)
     */
    private $bornPlanet;

    /**
     * @var integer
     *
     * @ORM\Column(name="family_id", type="integer", nullable=true)
     */
    private $familyId;

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

    public function __construct($id = null)
    {
        $this->id = $id;
        $this->preferences = new ArrayCollection();
        $this->knowledge = new ArrayCollection();
        $this->feelings = new Feelings($this);
    }

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
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * @param int $hours
     */
    public function setHours($hours)
    {
        $this->hours = $hours;
    }

    /**
     * @param int $hours
     */
    public function spendHours($hours)
    {
        $this->hours -= $hours;
    }

    /**
     * @return \AppBundle\Entity\Soul
     */
    public function getSoul()
    {
        return $this->soul;
    }

    /**
     * @param \AppBundle\Entity\Soul $soul
     */
    public function setSoul(Soul $soul)
    {
        $this->soul = $soul;
    }

    /**
     * @return Planet
     */
    public function getPlanet()
    {
        return $this->planet;
    }

    /**
     * @param Planet $planet
     */
    public function setPlanet($planet)
    {
        $this->planet = $planet;
    }

    /**
     * @return HumanPreference[]
     */
    public function getPreferences()
    {
        return $this->preferences;
    }

    /**
     * @param HumanPreference[] $preferences
     */
    public function setPreferences($preferences)
    {
        $this->preferences[] = $preferences;
    }

    /**
     * @param HumanPreference $preference
     */
    public function addPreference(HumanPreference $preference)
    {
        $this->preferences->add($preference);
    }

    /**
     * @return ArrayCollection
     */
    public function getKnowledge()
    {
        return $this->knowledge;
    }

    /**
     * @param ArrayCollection $knowledge
     */
    public function setKnowledge($knowledge)
    {
        $this->knowledge = $knowledge;
    }

    public function addKnowledge(Knowledge $knowledge) {
        $this->knowledge->add($knowledge);
    }

    /**
     * @return Feelings
     */
    public function getFeelings()
    {
        return $this->feelings;
    }

    /**
     * @param Feelings $feelings
     */
    public function setFeelings(Feelings $feelings)
    {
        $this->feelings = $feelings;
    }

    /**
     * @return int
     */
    public function getBornPhase()
    {
        return $this->bornPhase;
    }

    /**
     * @param int $bornPhase
     */
    public function setBornPhase($bornPhase)
    {
        $this->bornPhase = $bornPhase;
    }

    /**
     * @return int
     */
    public function getDeathTime()
    {
        return $this->deathTime;
    }

    /**
     * @param int $deathTime
     */
    public function setDeathTime($deathTime)
    {
        $this->deathTime = $deathTime;
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
     * @return Planet
     */
    public function getBornPlanet()
    {
        return $this->bornPlanet;
    }

    /**
     * @param Planet $bornPlanet
     */
    public function setBornPlanet($bornPlanet)
    {
        $this->bornPlanet = $bornPlanet;
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

