<?php

namespace AppBundle\Entity\Planet;
use Doctrine\ORM\Mapping as ORM;
/**
 * Region - map unit
 *
 * @ORM\Table(name="planet_regions")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Planet\RegionRepository")
 */
class Region
{
	/**
	 * @var Peak
	 *
	 * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Peak")
     * @ORM\JoinColumns{
     *  @ORM\JoinColumn(name="peak_center_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * }
	 * @ORM\GeneratedValue(strategy="NONE")
	 */
	private $peakCenter;

    /**
     * @var Peak
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Peak")
     * @ORM\JoinColumns{
     * @ORM\JoinColumn(name="peak_left_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * }
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $peakLeft;

    /**
     * @var Peak
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Peak")
     * @ORM\JoinColumns{
     * @ORM\JoinColumn(name="peak_right_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * }
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
	 * @var CurrentBuildingProject
	 *
	 * @ORM\OneToOne(targetEntity="CurrentBuildingProject", mappedBy="region")
	 */
	private $project;

    /**
     * @var HistoryBuildingProject[]
     *
     * @ORM\OneToMany(targetEntity="HistoryBuildingProject", mappedBy="region")
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
}

