<?php

namespace PlanetBundle\Entity\Job;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use PlanetBundle\Entity\Blueprint;
use PlanetBundle\Entity\Human;
use AppBundle\Entity\Notification\ProjectNotification;
use PlanetBundle\Entity\Region;
use AppBundle\Entity\ResourceDeposit;
use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Entity\RegionDependencyTrait;

/**
 * @ORM\MappedSuperclass
 */
abstract class Job
{
    use RegionDependencyTrait;

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
     * null => supervisor is region manager
     *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Human")
     * @ORM\JoinColumn(name="supervisor_id", referencedColumnName="id", nullable=true)
     */
    private $supervisor;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="priority", type="integer")
	 */
	private $priority = 0;

    /**
     * @var int|null
     *
     * null => unlimited (maximum what is possible)
     *
     * @ORM\Column(name="amount", type="integer", nullable=true)
     */
    private $amount;

    /**
     * @var int|null
     *
     * null => unlimited
     *
     * @ORM\Column(name="repetition", type="integer", nullable=true)
     */
    private $repetition;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \PlanetBundle\Entity\Human
     */
    public function getSupervisor()
    {
        return $this->supervisor;
    }

    /**
     * @param \PlanetBundle\Entity\Human $supervisor
     */
    public function setSupervisor(Human $supervisor)
    {
        $this->supervisor = $supervisor;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return int|null
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int|null $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return int|null
     */
    public function getRepetition()
    {
        return $this->repetition;
    }

    /**
     * @param int|null $repetition
     */
    public function setRepetition($repetition)
    {
        $this->repetition = $repetition;
    }
}

