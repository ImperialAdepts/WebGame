<?php

namespace AppBundle\Entity;

use AppBundle\Descriptor\TimeTransformator;
use AppBundle\Entity\Human\Feelings;
use AppBundle\Entity\Human\Title;
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
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Human\Title", mappedBy="humanHolder", cascade={"all"})
     */
    private $titles;

    /**
     * @var Title
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Human\Title")
     * @ORM\JoinColumn(name="title_id", referencedColumnName="id", nullable=true)
     */
    private $title;

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

    /**
     * @var Human[]
     *
     * @ORM\OneToMany(targetEntity="Human", mappedBy="motherHuman", cascade={"all"})
     */
    private $childrenByMother;

    /**
     * @var Human[]
     *
     * @ORM\OneToMany(targetEntity="Human", mappedBy="fatherHuman", cascade={"all"})
     */
    private $childrenByFather;

    public function __construct($id = null)
    {
        $this->id = $id;
        $this->childrenByFather = new ArrayCollection();
        $this->childrenByMother = new ArrayCollection();
        $this->preferences = new ArrayCollection();
        $this->knowledge = new ArrayCollection();
        $this->titles = new ArrayCollection();
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
     * @return ArrayCollection
     */
    public function getTitles()
    {
        return $this->titles;
    }

    /**
     * @param ArrayCollection $titles
     */
    public function setTitles($titles)
    {
        $this->titles = $titles;
    }

    public function addTitle(Title $title)
    {
        $title->setHumanHolder($this);
        $this->titles->add($title);
    }

    /**
     * @return Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param Title $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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

    public function isAlive() {
        return $this->getDeathTime() === null;
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

    /**
     * @return Human[]
     */
    public function getChildrenByMother()
    {
        return $this->childrenByMother;
    }

    /**
     * @param Human[] $childrenByMother
     */
    public function setChildrenByMother($childrenByMother)
    {
        $this->childrenByMother = $childrenByMother;
    }

    /**
     * @return Human[]
     */
    public function getChildrenByFather()
    {
        return $this->childrenByFather;
    }

    /**
     * @param Human[] $childrenByFather
     */
    public function setChildrenByFather($childrenByFather)
    {
        $this->childrenByFather = $childrenByFather;
    }

    /**
     * @return Human[]
     */
    public function getChildren() {
        foreach ($this->getChildrenByFather() as $human) {
            yield $human;
        }
        foreach ($this->getChildrenByMother() as $human) {
            yield $human;
        }
//        return array_merge(, $this->getChildrenByMother());
    }

    /**
     * @return float|int age in Earth years
     */
    public function getAge()
    {
        $bornTime = TimeTransformator::phaseToTimestamp($this->getBornPlanet(), $this->getBornPhase());
        $thisPhaseTime = TimeTransformator::phaseToTimestamp($this->getPlanet(), $this->getPlanet()->getLastPhaseUpdate());
        return TimeTransformator::timeLengthToAge($thisPhaseTime - $bornTime);
    }
}

