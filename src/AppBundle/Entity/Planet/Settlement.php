<?php

namespace AppBundle\Entity\Planet;

use AppBundle\Entity\ResourceDeposit;
use Doctrine\ORM\Mapping as ORM;
/**
 * Settlement - management unit
 *
 * @ORM\Table(name="planet_settlements")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Planet\SettlementRepository")
 */
class Settlement
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
	 * @var \AppBundle\Entity\Human
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Human")
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
	 */
	private $owner;

	/**
	 * @var \AppBundle\Entity\Human
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Human")
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
	 * @var ResourceDeposit[]
	 *
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\ResourceDeposit", mappedBy="settlement")
	 */
	private $resourceDeposits;

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
	 * Set owner
	 *
	 * @param \AppBundle\Entity\Human $owner
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
	 * @return \AppBundle\Entity\Human
	 */
	public function getOwner()
	{
		return $this->owner;
	}

	/**
	 * Set manager
	 *
	 * @param \AppBundle\Entity\Human $manager
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
	 * @return \AppBundle\Entity\Human
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

}

