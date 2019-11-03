<?php

namespace PlanetBundle\Entity;
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
    use PlanetEntity\Resource\BlueprintDependencyTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	/**
	 * @var Deposit
	 *
     * @ORM\ManyToOne(targetEntity="Deposit")
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
     * @return PeakDeposit
     */
    public function getOfferedResourceDeposit()
    {
        return $this->offeredResourceDeposit;
    }

    /**
     * @param PeakDeposit $offeredResourceDeposit
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
}

