<?php

namespace AppBundle\Entity\Planet;

use Doctrine\ORM\Mapping as ORM;

/**
 * OreDeposit
 *
 * @ORM\Table(name="planet_ore_deposits")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Planet\OreDepositRepository")
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
     * @var Region
     *
     * @ORM\ManyToOne(targetEntity="Region")
     * @ORM\JoinColumn(name="region_uuid", referencedColumnName="uuid")
     */
    private $region;

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
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param Region $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
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

