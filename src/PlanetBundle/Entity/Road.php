<?php

namespace PlanetBundle\Entity;
use AppBundle\Descriptor\Adapters\LandBuilding;
use AppBundle\Descriptor\Adapters\LivingBuilding;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Entity as GeneralEntity;
use PlanetBundle\Entity as PlanetEntity;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Table(name="roads")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\RoadRepository")
 */
class Road
{
    use PlanetEntity\Resource\BlueprintDependencyTrait;

	/**
	 * @var Peak
	 *
	 * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Peak")
     * @ORM\JoinColumn(name="peak_center_id", referencedColumnName="id", nullable=false)
	 * @ORM\GeneratedValue(strategy="NONE")
	 */
	private $peakFrom;

    /**
     * @var Peak
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Peak")
     * @ORM\JoinColumn(name="peak_left_id", referencedColumnName="id", nullable=false)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $peakTo;

	/**
	 * @var Settlement
	 *
	 * @ORM\ManyToOne(targetEntity="Settlement")
	 * @ORM\JoinColumn(name="settlement_id", referencedColumnName="id")
	 */
	private $belongsToSettlement;

    /**
     * @var integer
     *
     * @ORM\Column(name="free_capacity", type="integer")
     */
    private $freeCapacity;

    /**
     * @return Peak
     */
    public function getPeakFrom()
    {
        return $this->peakFrom;
    }

    /**
     * @param Peak $peakFrom
     */
    public function setPeakFrom($peakFrom)
    {
        $this->peakFrom = $peakFrom;
    }

    /**
     * @return Peak
     */
    public function getPeakTo()
    {
        return $this->peakTo;
    }

    /**
     * @param Peak $peakTo
     */
    public function setPeakTo($peakTo)
    {
        $this->peakTo = $peakTo;
    }

    /**
     * @return Settlement
     */
    public function getBelongsToSettlement()
    {
        return $this->belongsToSettlement;
    }

    /**
     * @param Settlement $belongsToSettlement
     */
    public function setBelongsToSettlement($belongsToSettlement)
    {
        $this->belongsToSettlement = $belongsToSettlement;
    }

    /**
     * @return int
     */
    public function getFreeCapacity()
    {
        return $this->freeCapacity;
    }

    /**
     * @param int $freeCapacity
     */
    public function setFreeCapacity($freeCapacity)
    {
        $this->freeCapacity = $freeCapacity;
    }

}

