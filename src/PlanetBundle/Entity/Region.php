<?php

namespace PlanetBundle\Entity;
use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Entity as GeneralEntity;
use AppBundle\PlanetConnection\DynamicPlanetConnector;
use AppBundle\UuidSerializer\UuidName;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr\Math;
use PlanetBundle\Builder\RegionTerrainTypeEnumBuilder;
use PlanetBundle\Concept\People;
use PlanetBundle\Entity as PlanetEntity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Region - map unit
 *
 * @ORM\Table(name="regions")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\RegionRepository")
 */
class Region implements ResourcefullInterface
{
    use SettlementDependencyTrait;
    use DepositOwnerTrait;

	/**
	 * @var Peak
	 *
	 * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Peak", cascade={"persist"})
     * @ORM\JoinColumn(name="peak_center_id", referencedColumnName="id", nullable=false)
	 * @ORM\GeneratedValue(strategy="NONE")
	 */
	private $peakCenter;

    /**
     * @var Peak
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Peak", cascade={"persist"})
     * @ORM\JoinColumn(name="peak_left_id", referencedColumnName="id", nullable=false)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $peakLeft;

    /**
     * @var Peak
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Peak", cascade={"persist"})
     * @ORM\JoinColumn(name="peak_right_id", referencedColumnName="id", nullable=false)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $peakRight;

    /**
     * @var RegionDeposit[]
     *
     * @ORM\OneToMany(targetEntity="RegionDeposit", mappedBy="region", cascade={"all"})
     */
    private $resourceDeposits;

	/**
	 * @var CurrentBuildingProject
	 *
     * TODO: predelat na seznam naplanovanych uloh
	 * @ ORM\OneToOne(targetEntity="CurrentBuildingProject", mappedBy="region")
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
        $this->resourceDeposits = new ArrayCollection();
    }

    /**
     * @return Peak
     */
    public function getPeakCenter()
    {
        return $this->peakCenter;
    }

    /**
     * @param Peak $peakCenter
     */
    public function setPeakCenter(Peak $peakCenter)
    {
        $this->peakCenter = $peakCenter;
    }

    /**
     * @return Peak
     */
    public function getPeakLeft()
    {
        return $this->peakLeft;
    }

    /**
     * @param Peak $peakLeft
     */
    public function setPeakLeft(Peak $peakLeft)
    {
        $this->peakLeft = $peakLeft;
    }

    /**
     * @return Peak
     */
    public function getPeakRight()
    {
        return $this->peakRight;
    }

    /**
     * @param Peak $peakRight
     */
    public function setPeakRight(Peak $peakRight)
    {
        $this->peakRight = $peakRight;
    }

    public function getCoords() {
        return $this->getPeakCenter()->getId()."_".$this->getPeakLeft()->getId()."_".$this->getPeakRight()->getId();
    }

    public function getName() {
        if ($this->getSettlement() == null) {
            return $this->getTerrainType(). ' '.$this->getCoords();
        } else {
            $count = 0;
            $type = $this->getTerrainType();
            foreach ($this->getSettlement()->getRegions() as $region) {
                if ($region->getTerrainType() == $type) {
                    $count++;
                }
                if ($region == $this) return $count.". ".$type." of ".UuidName::getSettlementName($this->getSettlement());
            }

        }
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

	public function getNPCCapacity() {
	    return floor(sqrt($this->getPeopleCount()/100));
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
     * @return Deposit[]
     */
    public function getResources()
    {
        return $this->resourceDeposits;
    }

    /**
     * @param $resourceDescriptor
     * @return Deposit|null
     */
    public function getResourceDeposit($resourceDescriptor)
    {
        if ($this->getDeposit() == null) {
            return null;
        }
        foreach ($this->getDeposit()->getResourceDescriptors() as $resourceDescriptor2) {
            if ($resourceDescriptor == $resourceDescriptor2) return $resourceDescriptor2;
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

    public function addResourceDeposit(PlanetEntity\Resource\Blueprint $blueprint, $amount = 1)
    {
        if (($deposit = $this->getResourceDeposit($blueprint->getResourceDescriptor())) != null) {
            $deposit->setAmount($deposit->getAmount() + $amount);
        } else {
            $deposit = new RegionDeposit();
            $deposit->setAmount($amount);
            $deposit->setResourceDescriptor($blueprint->getResourceDescriptor());
            $deposit->setBlueprint($blueprint);
            $deposit->setRegion($this);
            $this->getResources()->add($deposit);
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

    public function getTerrainType() {
	    $terrainBuilder = new RegionTerrainTypeEnumBuilder();
	    $terrainBuilder->setFertility($this->getFertility());
        return $terrainBuilder->getTerrainType();
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
        $radius = DynamicPlanetConnector::$PLANET->getDiameter() / 2;
        $planetWidth = DynamicPlanetConnector::$PLANET->getSurfaceGranularity();
	    $regionCount = $planetWidth*$planetWidth/2;
		return floor(4*pi()*$radius*$radius/$regionCount);
	}

	/**
	 * @return integer empty land space in m2
	 */
	public function getEmptyArea()
	{
        return $this->getArea() - $this->getUsedArea();
	}

    public function getUsedArea()
    {
        $area = 0;
        if (!$this->getDeposit()) {
            return $area;
        }
        foreach ($this->getDeposit()->filterByUseCase(\PlanetBundle\UseCase\LandBuilding::class) as $thing) {
            $area += $thing->getConceptAdapter()->getArea();
        }
        return $area;
    }

    /**
     * @return int
     */
    public function getPeopleCount() {
        if ($this->getDeposit() == null) return 0;
        $peopleCount = 0;
        foreach ($this->getDeposit()->filterByConcept(People::class) as $people) {
            $peopleCount += $people->getAmount();
        }
        return $peopleCount;
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
        if ($this->getResourceDepositAmount($resourceDescriptor) > $count) {
            $this->getResourceDeposit($resourceDescriptor)->setAmount($this->getResourceDeposit($resourceDescriptor)->getAmount() - $count);
        }
        if ($this->getResourceDeposit($resourceDescriptor) != null && $this->getResourceDepositAmount($resourceDescriptor) <= $count) {
            $this->getResourceDeposit($resourceDescriptor)->setAmount(0);
        }
    }
}

