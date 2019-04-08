<?php

namespace PlanetBundle\Entity;

use AppBundle\Descriptor\Adapters\AbstractResourceDepositAdapter;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Descriptor\UseCaseTraitEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * ResourceDeposit
 *
 * @ORM\Table(name="planet_resource_deposits")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResourceDepositRepository")
 */
class ResourceDeposit
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $resourceDescriptor;

    /**
     * @var Blueprint
     *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Blueprint")
     * @ORM\JoinColumn(fieldName="blueprint_id", referencedColumnName="id", nullable=true)
     */
    private $blueprint;

    /**
     * @var Region
     *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Region")
     * @ORM\JoinColumns(
     *  @ORM\JoinColumn(name="region_peak_center_id", referencedColumnName="peak_center_id", nullable=false),
     *  @ORM\JoinColumn(name="region_peak_left_id", referencedColumnName="peak_left_id", nullable=false),
     *  @ORM\JoinColumn(name="region_peak_right_id", referencedColumnName="peak_right_id", nullable=false)
     * )
     */
    private $region;

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
     * Set amount
     *
     * @param integer $amount
     *
     * @return ResourceDeposit
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
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

    /**
     * Set name
     *
     * @param string $resourceDescriptor
     *
     * @return ResourceDeposit
     */
    public function setResourceDescriptor($resourceDescriptor)
    {
        $this->resourceDescriptor = $resourceDescriptor;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getResourceDescriptor()
    {
        return $this->resourceDescriptor;
    }

    /**
     * Set blueprint
     *
     * @param Blueprint $blueprint
     *
     * @return ResourceDeposit
     */
    public function setBlueprint(Blueprint $blueprint)
    {
        $this->blueprint = $blueprint;

        return $this;
    }

    /**
     * Get blueprint
     *
     * @return Blueprint
     */
    public function getBlueprint()
    {
        return $this->blueprint;
    }

    /**
     * Set settlement
     *
     * @param Region $region
     *
     * @return ResourceDeposit
     */
    public function setRegion(Region $region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get settlement
     *
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    public function getUseCases() {
        if ($this->getBlueprint() != null) return $this->getBlueprint()->getUseCases();
        $resourceUseCases = [];
        if ($this->getResourceDescriptor() == ResourceDescriptorEnum::PEOPLE) {
            $resourceUseCases[] = UseCaseEnum::PEOPLE;
        }
        return $resourceUseCases;
    }

    /**
     * @param string $useCaseName useCase from UseCaseEnum
     * @return AbstractResourceDepositAdapter|null
     */
    public function asUseCase($useCaseName) {
        if (!in_array($useCaseName, $this->getUseCases())) {
            return null;
        }
        $adapterName = UseCaseEnum::getAdapter($useCaseName);
        if ($adapterName == null) {
            return null;
        }
        return new $adapterName($this);
    }
}

