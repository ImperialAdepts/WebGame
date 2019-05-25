<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Human\Feelings;
use AppBundle\Entity\rpg\HumanPreference;
use AppBundle\Entity\rpg\Knowledge;
use AppBundle\Entity\rpg\SoulPreference;
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

}

