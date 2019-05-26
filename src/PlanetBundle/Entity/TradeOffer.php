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
 * @ORM\Table(name="trade_offers")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\TradeRepository")
 */
class TradeOffer
{
    use SettlementDependencyTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	/**
	 * @var PeakResourceDeposit
	 *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\PeakResourceDeposit")
     * @ORM\JoinColumn(name="resource_deposit_id", referencedColumnName="id", nullable=false)
	 */
	private $offeredResourceDeposit;

    /**
     * @var int
     *
     * @ORM\Column(name="amount_requested", type="integer")
     */
    private $amountRequested;


    /**
     * @var Blueprint
     *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Blueprint")
     * @ORM\JoinColumn(fieldName="blueprint_id", referencedColumnName="id", nullable=true)
     */
    private $blueprint;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @return PeakResourceDeposit
     */
    public function getOfferedResourceDeposit()
    {
        return $this->offeredResourceDeposit;
    }

    /**
     * @param PeakResourceDeposit $offeredResourceDeposit
     */
    public function setOfferedResourceDeposit($offeredResourceDeposit)
    {
        $this->offeredResourceDeposit = $offeredResourceDeposit;
    }

    /**
     * @return int
     */
    public function getAmountRequested()
    {
        return $this->amountRequested;
    }

    /**
     * @param int $amountRequested
     */
    public function setAmountRequested($amountRequested)
    {
        $this->amountRequested = $amountRequested;
    }

    /**
     * @return Blueprint
     */
    public function getBlueprint()
    {
        return $this->blueprint;
    }

    /**
     * @param Blueprint $blueprint
     */
    public function setBlueprint($blueprint)
    {
        $this->blueprint = $blueprint;
    }

}

