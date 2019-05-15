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
     * @ORM\Column(name="all_life", type="integer", nullable=false)
     */
    private $allLife = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="last_period", type="integer", nullable=false)
     */
    private $lastPeriod = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="this_time", type="integer", nullable=false)
     */
    private $thisTime = 0;

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
        return $this->allLife;
    }

    /**
     * @param int $allLife
     */
    public function setAllLife($allLife)
    {
        $this->allLife = $allLife;
    }

    /**
     * @return int
     */
    public function getLastPeriod()
    {
        return $this->lastPeriod;
    }

    /**
     * @param int $lastPeriod
     */
    public function setLastPeriod($lastPeriod)
    {
        $this->lastPeriod = $lastPeriod;
    }

    /**
     * @return int
     */
    public function getThisTime()
    {
        return $this->thisTime;
    }

    /**
     * @param int $thisTime
     */
    public function setThisTime($thisTime)
    {
        $this->thisTime = $thisTime;
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
     * @param $time
     * @param $feelingChange
     * @param $description
     */
    public function change($time, $feelingChange, $description) {
        $this->setAllLife($this->getAllLife() + $feelingChange);
        $this->setLastPeriod($this->getLastPeriod() + $feelingChange);
        $this->setThisTime($this->getThisTime() + $feelingChange);

        $change = new FeelingChange();
        $change->setTime($time);
        $change->setHuman($this->getHuman());
        $change->setChange($feelingChange);
        $change->setDescription($description);
        $change->setFeelings($this);
        $this->history->add($change);
    }

}
