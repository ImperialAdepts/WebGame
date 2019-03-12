<?php

namespace AppBundle\Entity\Job;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Entity\Blueprint;
use AppBundle\Entity\Human;
use AppBundle\Entity\Notification\ProjectNotification;
use AppBundle\Entity\Planet\Region;
use AppBundle\Entity\ResourceDeposit;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="planet_region_jobs")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JobRepository")
 * @ORM\MappedSuperclass
 */
abstract class Job
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
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Planet\Region", inversedBy="project")
     * @ORM\JoinColumns(
     *  @ORM\JoinColumn(name="region_peak_center_id", referencedColumnName="peak_center_id", nullable=false),
     *  @ORM\JoinColumn(name="region_peak_left_id", referencedColumnName="peak_left_id", nullable=false),
     *  @ORM\JoinColumn(name="region_peak_right_id", referencedColumnName="peak_right_id", nullable=false)
     * )
     */
    private $region;

    /**
     * @var \AppBundle\Entity\Human
     *
     * null => supervisor is region manager
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Human")
     * @ORM\JoinColumn(name="supervisor_id", referencedColumnName="id", nullable=true)
     */
    private $supervisor;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="priority", type="integer")
	 */
	private $priority;

    /**
     * @var int|null
     *
     * null => unlimited (maximum what is possible)
     *
     * @ORM\Column(name="amount", type="integer", nullable=true)
     */
    private $amount;

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
    public function setRegion(Region $region)
    {
        $this->region = $region;
    }

    /**
     * @return \AppBundle\Entity\Human
     */
    public function getSupervisor()
    {
        return $this->supervisor;
    }

    /**
     * @param \AppBundle\Entity\Human $supervisor
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

}

