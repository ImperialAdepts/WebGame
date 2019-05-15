<?php

namespace AppBundle\Entity\Human;

use AppBundle\Entity\Human;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="human_feelings_history")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Human\FeelingChangeRepository")
 */
class FeelingChange
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
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="feeling_change", type="integer", nullable=false)
     */
    private $change;

    /**
     * @var integer
     *
     * @ORM\Column(name="time", type="integer", nullable=false)
     */
    private $time;

    /**
     * @var Human
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Human")
     * @ORM\JoinColumn(name="human_id", referencedColumnName="id", nullable=true)
     */
    private $human;

    /**
     * @var Feelings
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Human\Feelings", inversedBy="history")
     * @ORM\JoinColumn(name="human_id", referencedColumnName="human_id", nullable=true)
     */
    private $feelings;


    public function __construct($id = null)
    {
        $this->id = $id;
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getChange()
    {
        return $this->change;
    }

    /**
     * @param int $change
     */
    public function setChange($change)
    {
        $this->change = $change;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param int $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }


    /**
     * @return Human
     */
    public function getHuman()
    {
        return $this->human;
    }

    /**
     * @param Human $human
     */
    public function setHuman(Human $human)
    {
        $this->human = $human;
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
    public function setFeelings($feelings)
    {
        $this->feelings = $feelings;
    }

}

