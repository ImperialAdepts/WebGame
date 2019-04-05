<?php

namespace PlanetBundle\Entity;
use AppBundle\Descriptor\Adapters\LandBuilding;
use AppBundle\Descriptor\Adapters\LivingBuilding;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Entity\Blueprint;
use AppBundle\Entity\ResourceDeposit;
use Doctrine\ORM\Mapping as ORM;
/**
 * Region - map unit
 *
 * @ORM\Table(name="planet_regions")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\RegionRepository")
 */
class Region implements ResourcefullInterface
{
	/**
	 * @var Peak
	 *
	 * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Peak")
     * @ORM\JoinColumn(name="peak_center_id", referencedColumnName="id", nullable=false)
	 * @ORM\GeneratedValue(strategy="NONE")
	 */
	private $peakCenter;

    /**
     * @var Peak
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Peak")
     * @ORM\JoinColumn(name="peak_left_id", referencedColumnName="id", nullable=false)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $peakLeft;

    /**
     * @var Peak
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Peak")
     * @ORM\JoinColumn(name="peak_right_id", referencedColumnName="id", nullable=false)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $peakRight;

	/**
	 * @var Settlement
	 *
	 * @ORM\ManyToOne(targetEntity="Settlement", inversedBy="regions")
	 * @ORM\JoinColumn(name="settlement_id", referencedColumnName="id")
	 */
	private $settlement;

    /**
     * @var ResourceDeposit[]
     *
     * @ORM\OneToMany(targetEntity="PlanetBundle\Entity\ResourceDeposit", mappedBy="region", cascade={"all"})
     */
    private $resourceDeposits;

	/**
	 * @var CurrentBuildingProject
	 *
	 * @ORM\OneToOne(targetEntity="CurrentBuildingProject", mappedBy="region")
	 */
	private $project;

    /**
     * @var HistoryBuildingProject[]
     *
     * @ORM\OneToMany(targetEntity="HistoryBuildingProject", mappedBy="region", cascade={"remove"}, orphanRemoval=true)
     */
    private $projectHistory;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="fertility", type="float")
	 */
	private $fertility;

    /**
     * Region constructor.
     * @param Peak $peakCenter
     * @param Peak $peakLeft
     * @param Peak $peakRight
     */
    public function __construct(Peak $peakCenter, Peak $peakLeft, Peak $peakRight)
    {
        $this->peakCenter = $peakCenter;
        $this->peakLeft = $peakLeft;
        $this->peakRight = $peakRight;
    }

    /**
     * @return Peak
     */
    public function getPeakCenter()
    {
        return $this->peakCenter;
    }

    /**
     * @return Peak
     */
    public function getPeakLeft()
    {
        return $this->peakLeft;
    }

    /**
     * @return Peak
     */
    public function getPeakRight()
    {
        return $this->peakRight;
    }

    public function getCoords() {
        return $this->getPeakCenter()->getId()."_".$this->getPeakLeft()->getId()."_".$this->getPeakRight()->getId();
    }

	/**
	 * Set settlement
	 *
	 * @param Settlement $settlement
	 *
	 * @return Region
	 */
	public function setSettlement(Settlement $settlement)
	{
		$this->settlement = $settlement;

		return $this;
	}

	/**
	 * Get settlement
	 *
	 * @return Settlement
	 */
	public function getSettlement()
	{
		return $this->settlement;
	}

    /**
     * @return \AppBundle\Entity\ResourceDeposit[]
     */
    public function getResourceDeposits()
    {
        return $this->resourceDeposits;
    }

    /**
     * @param $resourceDescriptor
     * @return ResourceDeposit|null
     */
    public function getResourceDeposit($resourceDescriptor)
    {
        foreach ($this->getResourceDeposits() as $deposit) {
            if ($deposit->getResourceDescriptor() == $resourceDescriptor) return $deposit;
        }
        return null;
    }

    /**
     * @param \AppBundle\Entity\ResourceDeposit[] $resourceDeposits
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
            $deposit = new ResourceDeposit();
            $deposit->setAmount($amount);
            $deposit->setResourceDescriptor($blueprint->getResourceDescriptor());
            $deposit->setBlueprint($blueprint);
            $deposit->setRegion($this);
            $this->getResourceDeposits()->add($deposit);
        }
    }

	/**
	 * @return CurrentBuildingProject
	 */
	public function getProject()
	{
		return $this->project;
	}

	/**
	 * @param CurrentBuildingProject $project
	 */
	public function setProject(CurrentBuildingProject $project)
	{
		$this->project = $project;
	}

    /**
     * @return HistoryBuildingProject[]
     */
    public function getProjectHistory()
    {
        return $this->projectHistory;
    }

    /**
     * @param HistoryBuildingProject[] $projectHistory
     */
    public function setProjectHistory($projectHistory)
    {
        $this->projectHistory = $projectHistory;
    }


	/**
	 * Set fertility
	 *
	 * @param float $fertility
	 *
	 * @return Region
	 */
	public function setFertility($fertility)
	{
		$this->fertility = $fertility;

		return $this;
	}

	/**
	 * Get fertility
	 *
	 * @return float
	 */
	public function getFertility()
	{
		return $this->fertility;
	}

	/**
	 * Get ores
	 *
	 * @return OreDeposit[]
	 */
	public function getAvailableOreDeposits()
	{
		return array_merge(
		    $this->getPeakCenter()->getOreDeposits(),
		    $this->getPeakLeft()->getOreDeposits(),
		    $this->getPeakRight()->getOreDeposits()
        );
	}

	/**
	 * @return integer land space in m2
	 */
	public function getArea()
	{
		// TODO vylovit z informaci o planete
		return 200;
	}

	/**
	 * @return integer empty land space in m2
	 */
	public function getEmptyArea()
	{
		if ($this->getSettlement() === null) {
			return $this->getArea();
		} else {
			// TODO: vytahnout spravnou hodnotu ze settlementu
			return $this->getArea() / 2;
		}
	}

    public function getUsedArea()
    {
        return LandBuilding::countUsedArea(LandBuilding::in($this));
    }

    /**
     * @return int
     */
    public function getPeopleCount() {
        $deposit = $this->getResourceDeposit(ResourceDescriptorEnum::PEOPLE);
        if ($deposit == null) return 0;
        return $deposit->getAmount();
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
}

