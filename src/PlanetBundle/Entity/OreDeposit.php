<?php

namespace PlanetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OreDeposit
 *
 * @ORM\Table(name="ore_deposits")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\OreDepositRepository")
 */
class OreDeposit
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Peak
     *
     * @ORM\ManyToOne(targetEntity="Peak")
     * @ORM\JoinColumn(name="peak_id", referencedColumnName="id")
     */
    private $peak;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="quality", type="integer")
     */
    private $quality;

    /**
     * @var integer
     *
     * @ORM\Column(name="amount", type="bigint")
     */
    private $amount;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Peak
     */
    public function getPeak()
    {
        return $this->peak;
    }

    /**
     * @param Peak $peak
     */
    public function setPeak(Peak $peak)
    {
        $this->peak = $peak;
    }


    /**
     * Set type
     *
     * @param string $type
     *
     * @return OreDeposit
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set quality
     *
     * @param float $quality
     *
     * @return OreDeposit
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * Get quality
     *
     * @return float
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return OreDeposit
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }
}

