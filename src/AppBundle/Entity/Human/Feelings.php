<?php

namespace AppBundle\Entity\Human;

use AppBundle\Entity\Human;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="human_feelings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Human\FeelingChangeRepository")
 */
class Feelings
{
    /**
     * @var Human
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Human", inversedBy="feelings")
     * @ORM\JoinColumn(name="human_id", referencedColumnName="id", nullable=false)
     */
    private $human;

    /**
     * @var integer
     *
     * @ORM\Column(name="all_life_happiness", type="integer", nullable=false)
     */
    private $allLifeHappiness = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="all_life_sadness", type="integer", nullable=false)
     */
    private $allLifeSadness = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="last_period_happiness", type="integer", nullable=false)
     */
    private $lastPeriodHappiness = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="last_period_sadness", type="integer", nullable=false)
     */
    private $lastPeriodSadness = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="this_time_happiness", type="integer", nullable=false)
     */
    private $thisTimeHappiness = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="this_time_sadness", type="integer", nullable=false)
     */
    private $thisTimeSadness = 0;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Human\FeelingChange", mappedBy="feelings", cascade={"all"})
     */
    private $history;

    /**
     * Feelings constructor.
     * @param Human $human
     */
    public function __construct(Human $human)
    {
        $this->human = $human;
        $this->history = new ArrayCollection();
    }

    /**
     * @return Human
     */
    public function getHuman()
    {
        return $this->human;
    }

    /**
     * @return int
     */
    public function getAllLife()
    {
        return $this->getAllLifeHappiness() - $this->getAllLifeSadness();
    }

    /**
     * @return int
     */
    public function getLastPeriod()
    {
        return $this->getLastPeriodHappiness() - $this->getLastPeriodSadness();
    }

    /**
     * @return int
     */
    public function getThisTime()
    {
        return $this->getThisTimeHappiness() - $this->getThisTimeSadness();
    }


    /**
     * @return int
     */
    public function getAllLifeHappiness()
    {
        return $this->allLifeHappiness;
    }

    /**
     * @param int $allLifeHappiness
     */
    public function setAllLifeHappiness($allLifeHappiness)
    {
        $this->allLifeHappiness = $allLifeHappiness;
    }

    /**
     * @return int
     */
    public function getAllLifeSadness()
    {
        return $this->allLifeSadness;
    }

    /**
     * @param int $allLifeSadness
     */
    public function setAllLifeSadness($allLifeSadness)
    {
        $this->allLifeSadness = $allLifeSadness;
    }

    /**
     * @return int
     */
    public function getLastPeriodHappiness()
    {
        return $this->lastPeriodHappiness;
    }

    /**
     * @param int $lastPeriodHappiness
     */
    public function setLastPeriodHappiness($lastPeriodHappiness)
    {
        $this->lastPeriodHappiness = $lastPeriodHappiness;
    }

    /**
     * @return int
     */
    public function getLastPeriodSadness()
    {
        return $this->lastPeriodSadness;
    }

    /**
     * @param int $lastPeriodSadness
     */
    public function setLastPeriodSadness($lastPeriodSadness)
    {
        $this->lastPeriodSadness = $lastPeriodSadness;
    }

    /**
     * @return int
     */
    public function getThisTimeHappiness()
    {
        return $this->thisTimeHappiness;
    }

    /**
     * @param int $thisTimeHappiness
     */
    public function setThisTimeHappiness($thisTimeHappiness)
    {
        $this->thisTimeHappiness = $thisTimeHappiness;
    }

    /**
     * @return int
     */
    public function getThisTimeSadness()
    {
        return $this->thisTimeSadness;
    }

    /**
     * @param int $thisTimeSadness
     */
    public function setThisTimeSadness($thisTimeSadness)
    {
        $this->thisTimeSadness = $thisTimeSadness;
    }

    /**
     * @return FeelingChange[]
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * @param ArrayCollection $history
     */
    public function setHistory($history)
    {
        $this->history = $history;
    }

    /**
     * @param $feelingChange
     * @param $description
     * @param array $descriptionData
     * @param Event|null $cause
     */
    public function change($feelingChange, $description, array $descriptionData = [], Event $cause = null) {
        if ($feelingChange >= 0) {
            $this->setAllLifeHappiness($this->getAllLifeHappiness() + $feelingChange);
            $this->setLastPeriodHappiness($this->getLastPeriodHappiness() + $feelingChange);
            $this->setThisTimeHappiness($this->getThisTimeHappiness() + $feelingChange);
        } else {
            $this->setAllLifeSadness($this->getAllLifeSadness() + abs($feelingChange));
            $this->setLastPeriodSadness($this->getLastPeriodSadness() + abs($feelingChange));
            $this->setThisTimeSadness($this->getThisTimeSadness() + abs($feelingChange));
        }

        $change = new FeelingChange();
        $change->setTime(time());
        $change->setHuman($this->getHuman());
        $change->setPlanet($this->getHuman()->getPlanet());
        $change->setPlanetPhase($this->getHuman()->getPlanet()->getLastPhaseUpdate());
        $change->setChange($feelingChange);
        $change->setDescription($description);
        if (!empty($descriptionData)) {
            $change->setDescriptionData($descriptionData);
        }
        if ($cause != null) {
            $change->setCauseEvent($cause);
        }
        $change->setFeelings($this);
        $this->history->add($change);
    }

}
