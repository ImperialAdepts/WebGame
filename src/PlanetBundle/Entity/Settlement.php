<?php

namespace PlanetBundle\Entity;

use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\UuidSerializer\UuidName;
use Doctrine\ORM\Mapping as ORM;
/**
 * Settlement - management unit
 *
 * @ORM\Table(name="settlements")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\SettlementRepository")
 */
class Settlement implements ResourcefullInterface
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
	 * @var \PlanetBundle\Entity\Human
	 *
	 * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Human")
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
	 */
	private $owner;

	/**
	 * @var \PlanetBundle\Entity\Human
	 *
	 * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Human")
	 * @ORM\JoinColumn(name="manager_id", referencedColumnName="id")
	 */
	private $manager;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="type", type="string", length=255)
	 */
	private $type;

	/**
	 * @var Region[]
	 *
	 * @ORM\OneToMany(targetEntity="Region", mappedBy="settlement")
	 */
	private $regions;

    /**
     * @var Peak[]
     *
     * @ORM\OneToMany(targetEntity="PlanetBundle\Entity\Peak", mappedBy="settlement")
     */
    private $peaks;

    /**
     * @var Peak
     *
     * @ORM\OneToOne(targetEntity="PlanetBundle\Entity\Peak")
     * @ORM\JoinColumn(name="administrative_peak_id", referencedColumnName="id", nullable=false)
     */
    private $administrativeCenter;

    /**
     * @var Peak
     *
     * @ORM\OneToOne(targetEntity="PlanetBundle\Entity\Peak")
     * @ORM\JoinColumn(name="trade_peak_id", referencedColumnName="id", nullable=true)
     */
    private $tradeCenter;

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function getName() {
	    return UuidName::getSettlementName($this). ' settlement';
    }

	/**
	 * Set owner
	 *
	 * @param \PlanetBundle\Entity\Human $owner
	 *
	 * @return Settlement
	 */
	public function setOwner($owner)
	{
		$this->owner = $owner;

		return $this;
	}

	/**
	 * Get owner
	 *
	 * @return \PlanetBundle\Entity\Human
	 */
	public function getOwner()
	{
		return $this->owner;
	}

	/**
	 * Set manager
	 *
	 * @param \PlanetBundle\Entity\Human $manager
	 *
	 * @return Settlement
	 */
	public function setManager($manager)
	{
		$this->manager = $manager;

		return $this;
	}

	/**
	 * Get manager
	 *
	 * @return \PlanetBundle\Entity\Human
	 */
	public function getManager()
	{
		return $this->manager;
	}

	/**
	 * Set type
	 *
	 * @param string $type
	 *
	 * @return Settlement
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

	// TODO: predelat tak, aby byl hlavni region nastavitelny

    /**
     * @return Region
     */
	public function getMainRegion() {
        foreach ($this->getRegions() as $region) {
            return $region;
        }
    }

	/**
	 * @return Region[]
	 */
	public function getRegions()
	{
		return $this->regions;
	}

	/**
	 * @param Region[] $regions
	 */
	public function setRegions($regions)
	{
		$this->regions = $regions;
	}

    /**
     * @return Peak[]
     */
    public function getPeaks()
    {
        return $this->peaks;
    }

    /**
     * @param Peak[] $peaks
     */
    public function setPeaks($peaks)
    {
        $this->peaks = $peaks;
    }

    /**
     * @return Peak
     */
    public function getAdministrativeCenter()
    {
        return $this->administrativeCenter;
    }

    /**
     * @param Peak $administrativeCenter
     */
    public function setAdministrativeCenter($administrativeCenter)
    {
        $this->administrativeCenter = $administrativeCenter;
    }

    /**
     * @return Peak
     */
    public function getTradeCenter()
    {
        return $this->tradeCenter;
    }

    /**
     * @param Peak $tradeCenter
     */
    public function setTradeCenter($tradeCenter)
    {
        $this->tradeCenter = $tradeCenter;
    }

	/**
	 * @param $resourceDescriptor
	 * @return ResourceDeposit[] region_coords => ResourceDeposit[]
	 */
	public function getResourceDeposits($resourceDescriptor = null)
	{
	    $deposits = [];
	    /** @var Region $region */
        foreach ($this->getRegions() as $region) {
            if ($resourceDescriptor != null) {
                if (($localDeposit = $region->getResourceDeposit($resourceDescriptor)) != null) {
                    $deposits[] = $localDeposit;
                }
            } else {
                foreach ($region->getResourceDeposits() as $deposit) {
                    $deposits[] = $deposit;
                }
            }
        }
        /** @var Peak $peak */
        foreach ($this->getPeaks() as $peak) {
            if ($resourceDescriptor != null) {
                if (($localDeposit = $peak->getResourceDeposit($resourceDescriptor)) != null) {
                    $deposits[] = $localDeposit;
                }
            } else {
                foreach ($peak->getResourceDeposits() as $deposit) {
                    $deposits[] = $deposit;
                }
            }
        }
		return $deposits;
	}

    /**
     * @return int
     */
	public function getPeopleCount() {
	    $counter = 0;
	    /** @var Region $region */
        foreach ($this->getRegions() as $region) {
            $counter += $region->getPeopleCount();
        }
        return $counter;
    }

    /**
     * @param string $resourceDescriptor
     * @return int
     */
    public function getResourceDepositAmount($resourceDescriptor)
    {
        $count = 0;
        foreach ($this->getResourceDeposits($resourceDescriptor) as $deposit) {
            $count += $deposit->getAmount();
        }
        return $count;
    }

    /**
     * @param Blueprint $blueprint
     * @param int $count
     */
    public function addResourceDeposit(Blueprint $blueprint, $count = 1)
    {
        $this->getMainRegion()->addResourceDeposit($blueprint, $count);
    }

    /**
     * @param $resourceDescriptor
     * @param int $count
     */
    public function consumeResourceDepositAmount($resourceDescriptor, $count = 1)
    {
        $this->getMainRegion()->consumeResourceDepositAmount($resourceDescriptor, $count);
    }
}

