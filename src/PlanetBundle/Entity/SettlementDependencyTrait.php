<?php
namespace PlanetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait SettlementDependencyTrait
{
    /**
     * @var Settlement
     *
     * @ORM\ManyToOne(targetEntity="Settlement", inversedBy="regions")
     * @ORM\JoinColumn(name="settlement_id", referencedColumnName="id", nullable=true)
     */
    private $settlement;

    /**
     * @return Settlement
     */
    public function getSettlement()
    {
        return $this->settlement;
    }

    /**
     * @param Settlement $settlement
     */
    public function setSettlement(Settlement $settlement)
    {
        $this->settlement = $settlement;
    }

}