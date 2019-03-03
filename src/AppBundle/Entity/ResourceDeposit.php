<?php

namespace AppBundle\Entity;

use AppBundle\Descriptor\Adapters\AbstractResourceDepositAdapter;
use AppBundle\Descriptor\UseCaseEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * ResourceDeposit
 *
 * @ORM\Table(name="resource_deposits")
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $resourceDescriptor;

    /**
     * @var Blueprint
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Blueprint")
     * @ORM\JoinColumn(fieldName="blueprint_id", referencedColumnName="id", nullable=true)
     */
    private $blueprint;

    /**
     * @var Planet\Settlement
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Planet\Settlement")
     * @ORM\JoinColumn(fieldName="settlement_id", referencedColumnName="id")
     */
    private $settlement;

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
     * Set name
     *
     * @param string $resourceDescriptor
     *
     * @return Resource
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
     * @return Resource
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
     * @param Planet\Settlement $settlement
     *
     * @return ResourceDeposit
     */
    public function setSettlement(Planet\Settlement $settlement)
    {
        $this->settlement = $settlement;

        return $this;
    }

    /**
     * Get settlement
     *
     * @return Planet\Settlement
     */
    public function getSettlement()
    {
        return $this->settlement;
    }

    public function getUseCases() {
        if ($this->getBlueprint() != null) return $this->getBlueprint()->getUseCases();
        return UseCaseEnum::RESOURCE_DEPOSIT;
    }

    public function getSpace() {
        if ($this->getBlueprint() == null) return 0;
        return $this->amount * $this->getBlueprint()->getSpace();
    }

    public function getWeight() {
        if ($this->getBlueprint() == null) return 0;
        return $this->amount * $this->getBlueprint()->getWeight();
    }

    /**
     * @param string $useCaseName useCase from UseCaseEnum
     * @return AbstractResourceDepositAdapter|null
     */
    public function asUseCase($useCaseName) {
        $adapterName = UseCaseEnum::getAdapter($useCaseName);
        if ($adapterName == null) {
            return null;
        }
        return new $adapterName($this);
    }
}

