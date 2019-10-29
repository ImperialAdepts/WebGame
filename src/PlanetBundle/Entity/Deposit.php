<?php

namespace PlanetBundle\Entity;

use AppBundle\Descriptor\Adapters\AbstractResourceDepositAdapter;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Descriptor\UseCaseTraitEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Entity\Resource\Blueprint;
use PlanetBundle\Entity\Resource\DepositInterface;
use PlanetBundle\Entity\Resource\ResourceDescriptor;
use PlanetBundle\Entity\Resource\Thing;

/**
 * ResourceDeposit
 *
 * @ORM\Entity()
 * @ORM\Table(name="deposits")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="owner_type", type="string")
 * @ORM\DiscriminatorMap({"region" = "RegionDeposit", "peak" = "PeakDeposit", "standardized" = "StandardizedDeposit"})
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
     * @ORM\OneToMany(targetEntity="PlanetBundle\Entity\Resource\ResourceDescriptor", mappedBy="deposit", cascade={"persist", "remove"})
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
        $resourceDescriptor->setDeposit($this);
    }

    /**
     * @param ResourceDescriptor[] $resourceDescriptors
     */
    public function setResourceDescriptors($resourceDescriptors)
    {
        $this->resourceDescriptors = $resourceDescriptors;
    }

    /**
     * @param string $useCase trait class
     * @return Thing[]
     */
    public function filterByUseCase($useCase)
    {
        $descriptors = [];
        foreach ($this->getResourceDescriptors() as $descriptor) {
            if ($descriptor instanceof Thing && $descriptor->hasUsecase($useCase)) {
                $descriptors[] = $descriptor;
            }
        }
        return $descriptors;
    }

    /**
     * @param Blueprint $blueprint
     * @return Thing[]
     */
    public function filterByBlueprint(Blueprint $blueprint)
    {
        $descriptors = [];
        foreach ($this->getResourceDescriptors() as $descriptor) {
            if ($descriptor instanceof Thing && $descriptor->getBlueprint() === $blueprint) {
                $descriptors[] = $descriptor;
            }
        }
        return $descriptors;
    }

    public function filterByConcept($concept)
    {
        $descriptors = [];
        foreach ($this->getResourceDescriptors() as $descriptor) {
            if ($descriptor instanceof Thing && $descriptor->getBlueprint()->getConcept() === $concept) {
                $descriptors[] = $descriptor;
            }
        }
        return $descriptors;
    }
}

