<?php

namespace AppBundle\Entity\Planet;
use Doctrine\ORM\Mapping as ORM;
/**
 * Peak - map unit
 *
 * @ORM\Table(name="planet_peaks")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Planet\PeakRepository")
 */
class Peak
{
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", length=30)
	 * @ORM\Id
	 * @ ORM\GeneratedValue(strategy="")
	 */
	private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="x", type="integer", length=30)
     */
	private $xcoord;

    /**
     * @var integer
     *
     * @ORM\Column(name="y", type="integer", length=30)
     */
	private $ycoord;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="height", type="integer")
	 */
	private $height = 0;

    /**
     * @var OreDeposit[]
     * Known deposits, not all!
     *
     * @ORM\OneToMany(targetEntity="OreDeposit", mappedBy="region")
     */
    private $oreDeposits;

    /**
     * Peak constructor.
     * @param int $id
     */
    public function __construct($id = null)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getXcoord()
    {
        return $this->xcoord;
    }

    /**
     * @param int $xcoord
     */
    public function setXcoord($xcoord)
    {
        $this->xcoord = $xcoord;
    }

    /**
     * @return int
     */
    public function getYcoord()
    {
        return $this->ycoord;
    }

    /**
     * @param int $ycoord
     */
    public function setYcoord($ycoord)
    {
        $this->ycoord = $ycoord;
    }


	/**
	 * Set height
	 *
	 * @param integer $height
	 *
	 * @return Region
	 */
	public function setHeight($height)
	{
		$this->height = $height;

		return $this;
	}

	/**
	 * Get height
	 *
	 * @return int
	 */
	public function getHeight()
	{
		return $this->height;
	}

    /**
     * @return OreDeposit[]
     */
    public function getOreDeposits()
    {
        return $this->oreDeposits;
    }

    /**
     * @param OreDeposit[] $oreDeposits
     */
    public function setOreDeposits(OreDeposit $oreDeposits)
    {
        $this->oreDeposits = $oreDeposits;
    }


}

