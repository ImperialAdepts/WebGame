<?php

namespace PlanetBundle\Entity;

use AppBundle\Descriptor\Adapters\AbstractResourceDepositAdapter;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Descriptor\UseCaseTraitEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Entity\Resource\DepositInterface;
use PlanetBundle\Entity\Resource\ResourceDescriptor;

/**
 * ResourceDeposit
 *
 * @ORM\Entity()
 * @ORM\Table(name="deposits")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="owner_type", type="string")
 * @ORM\DiscriminatorMap({"region" = "RegionDeposit", "peak" = "PeakDeposit"})
 */
abstract class Deposit implements DepositInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ResourceDescriptor[]
     *
     * @ORM\OneToMany(targetEntity="PlanetBundle\Entity\Resource\ResourceDescriptor", mappedBy="deposit")
     */
    private $resourceDescriptors;

    public function __construct()
    {
        $this->resourceDescriptors = new ArrayCollection();
    }

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
     * @return ResourceDescriptor[]
     */
    public function getResourceDescriptors()
    {
        return $this->resourceDescriptors;
    }

    public function addResourceDescriptors(ResourceDescriptor $resourceDescriptor)
    {
        $this->resourceDescriptors->add($resourceDescriptor);
    }

    /**
     * @param ResourceDescriptor[] $resourceDescriptors
     */
    public function setResourceDescriptors($resourceDescriptors)
    {
        $this->resourceDescriptors = $resourceDescriptors;
    }

}

