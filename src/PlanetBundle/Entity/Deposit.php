<?php

namespace PlanetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use PlanetBundle\Concept\Food;
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
 * @ORM\DiscriminatorMap({"region" = "RegionDeposit", "peak" = "PeakDeposit", "standardized" = "PlanetBundle\Entity\Resource\StandardizedDeposit"})
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

    public abstract function getResourceHandler();

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

    /**
     * @param ResourceDescriptor[] $descriptors
     * @return int
     */
    public static function sumAmounts($descriptors) {
        $count = 0;
        foreach ($descriptors as $descriptor) {
            $count += $descriptor->getAmount();
        }
        return $count;
    }

    /**
     * @param Thing[] $things
     * @param callable $callback
     * @return int
     */
    public static function sumCallbacks($things, callable $callback) {
        $sum = 0;
        foreach ($things as $thing) {
            $sum += $callback($thing->getConceptAdapter()) * $thing->getAmount();
        }
        return $sum;
    }

    /**
     * @param Thing[] $things
     * @return float|int
     */
    public static function countVariety($things) {
        if (count($things) == 0) return 0;
        $average = 0;
        $average = $average / count($things);
        $varietyFactor = 0;

        $countsByBlueprint = [];
        /** @var Thing $thing */
        foreach ($things as $thing) {
            if (!isset($countsByBlueprint[$thing->getBlueprint()->getConcept()])) {
                $countsByBlueprint[$thing->getBlueprint()->getId()] = 0;
            }
            $countsByBlueprint[$thing->getBlueprint()->getId()] += $thing->getAmount();
        }

        foreach ($countsByBlueprint as $concept => $amount) {
            if ($amount > $average) {
                $varietyFactor += 1;
            } else {
                $varietyFactor += ($average / $amount);
            }
        }
        return $varietyFactor;
    }

    public function __toString()
    {
        return get_class($this)."#".$this->getId();
    }


}

