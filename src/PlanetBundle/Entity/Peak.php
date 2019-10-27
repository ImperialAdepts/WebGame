<?php

namespace PlanetBundle\Entity;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\ResourcefullInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Entity\Resource\Blueprint;

/**
 * Peak - map unit
 *
 * @ORM\Table(name="peaks")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\PeakRepository")
 */
class Peak implements ResourcefullInterface
{
    use SettlementDependencyTrait;
    use DepositDependencyTrait;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", length=30)
	 * @ORM\Id()
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
     * @var PeakDeposit[]
     *
     * @ORM\OneToMany(targetEntity="PeakDeposit", mappedBy="peak", cascade={"all"})
     */
    private $resourceDeposits;

    /**
     * @var Human[]
     *
     * @ORM\OneToMany(targetEntity="PlanetBundle\Entity\Human", mappedBy="currentPeakPosition", cascade={"all"})
     */
    private $humans;

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
     * @return Human[]
     */
    public function getHumans()
    {
        return $this->humans;
    }

    /**
     * @param Human[] $humans
     */
    public function setHumans($humans)
    {
        $this->humans = $humans;
    }

    /**
     * @return int
     */
    public function getPeopleCount() {
        $deposit = $this->getResourceDeposit(ResourceDescriptorEnum::PEOPLE);
        if ($deposit == null) return 0;
        return $deposit->getAmount();
    }

    public function getNPCCapacity() {
        return floor(sqrt($this->getPeopleCount()/100));
    }

    /**
     * @param $resourceDescriptor
     * @return PeakDeposit|null
     */
    public function getResourceDeposit($resourceDescriptor)
    {
        foreach ($this->getDeposit() as $deposit) {
            if ($deposit->getResourceDescriptor() == $resourceDescriptor) return $deposit;
        }
        return null;
    }

    /**
     * @param \AppBundle\Entity\PeakResourceDeposit[] $deposit
     */
    public function setDeposit($deposit)
    {
        $this->deposit = $deposit;
    }

    public function addResourceDeposit(Blueprint $blueprint, $amount = 1)
    {
        if (($deposit = $this->getResourceDeposit($blueprint->getResourceDescriptor())) != null) {
            $deposit->setAmount($deposit->getAmount() + $amount);
        } else {
            $deposit = new PeakDeposit();
            $deposit->setAmount($amount);
            $deposit->setResourceDescriptor($blueprint->getResourceDescriptor());
            $deposit->setBlueprint($blueprint);
            $deposit->setPeak($this);
            $this->getDeposit()->add($deposit);
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

