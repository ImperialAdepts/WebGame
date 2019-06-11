<?php

namespace PlanetBundle\Entity;
use AppBundle\Descriptor\ResourcefullInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * Peak - map unit
 *
 * @ORM\Table(name="peaks")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\PeakRepository")
 */
class Peak implements ResourcefullInterface
{
    use SettlementDependencyTrait;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", length=30)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
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
     * @var PeakResourceDeposit[]
     *
     * @ORM\OneToMany(targetEntity="PlanetBundle\Entity\PeakResourceDeposit", mappedBy="peak", cascade={"all"})
     */
    private $resourceDeposits;

    /**
     * Peak constructor.
     * @param int $id
     */
    public function __construct($id = null)
    {
        $this->id = $id;
        $this->resourceDeposits = new ArrayCollection();
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

    /**
     * @return PeakResourceDeposit[]
     */
    public function getResourceDeposits()
    {
        return $this->resourceDeposits;
    }

    /**
     * @param $resourceDescriptor
     * @return PeakResourceDeposit|null
     */
    public function getResourceDeposit($resourceDescriptor)
    {
        foreach ($this->getResourceDeposits() as $deposit) {
            if ($deposit->getResourceDescriptor() == $resourceDescriptor) return $deposit;
        }
        return null;
    }

    /**
     * @param \AppBundle\Entity\PeakResourceDeposit[] $resourceDeposits
     */
    public function setResourceDeposits($resourceDeposits)
    {
        $this->resourceDeposits = $resourceDeposits;
    }

    public function addResourceDeposit(Blueprint $blueprint, $amount = 1)
    {
        if (($deposit = $this->getResourceDeposit($blueprint->getResourceDescriptor())) != null) {
            $deposit->setAmount($deposit->getAmount() + $amount);
        } else {
            $deposit = new PeakResourceDeposit();
            $deposit->setAmount($amount);
            $deposit->setResourceDescriptor($blueprint->getResourceDescriptor());
            $deposit->setBlueprint($blueprint);
            $deposit->setPeak($this);
            $this->getResourceDeposits()->add($deposit);
        }
    }

    /**
     * @param string $resourceDescriptor
     * @return int
     */
    public function getResourceDepositAmount($resourceDescriptor)
    {
        if ($this->getResourceDeposit($resourceDescriptor) != null) {
            return $this->getResourceDeposit($resourceDescriptor)->getAmount();
        }
        return 0;
    }

    /**
     * @param $resourceDescriptor
     * @param int $count
     */
    public function consumeResourceDepositAmount($resourceDescriptor, $count = 1)
    {
        $this->getResourceDeposit($resourceDescriptor)->setAmount($this->getResourceDeposit($resourceDescriptor)->getAmount() - $count);
    }

}

