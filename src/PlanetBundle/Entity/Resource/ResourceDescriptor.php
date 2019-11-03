<?php

namespace PlanetBundle\Entity\Resource;

use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Entity\Deposit;
use PlanetBundle\Entity\DepositDependencyTrait;

/**
 * ResourceDescriptor
 *
 * @ORM\Entity()
 * @ORM\Table(name="resource_descriptors")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="descriptor_type", type="string")
 * @ORM\DiscriminatorMap({"resource" = "Resource", "thing" = "Thing"})
 */
abstract class ResourceDescriptor
{
    use DepositDependencyTrait;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var int
     *
     * @ORM\Column(name="work_hours", type="integer")
     */
    private $workHours = 0;

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
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getWorkHours()
    {
        return $this->workHours;
    }

    /**
     * @param int $workHours
     */
    public function setWorkHours($workHours)
    {
        $this->workHours = $workHours;
    }

    public function cloneToDeposit(Deposit $targetDeposit) {
        if ($this instanceof Thing) {
            $descriptor = new Thing();
            $descriptor->setAmount($this->getAmount());
            $descriptor->setBlueprint($this->getBlueprint());
            $descriptor->setDescription($this->getDescription());
        } elseif ($this instanceof Resource) {
            $descriptor = new Resource();
            $descriptor->setType($this->getType());
            $descriptor->setAmount($this->getAmount());
        }
    }
}

