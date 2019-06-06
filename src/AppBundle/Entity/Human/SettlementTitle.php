<?php
namespace AppBundle\Entity\Human;

use AppBundle\Entity\Human;
use AppBundle\Entity\SolarSystem\Planet;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="human_land_titles")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Human\TitleRepository")
 */
class SettlementTitle extends Title
{
    /**
     * @var Planet
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SolarSystem\Planet")
     * @ORM\JoinColumn(name="planet_id", referencedColumnName="id", nullable=true)
     */
    private $settlementPlanet;

    /**
     * @var int
     *
     * @ORM\Column(name="settlement_id", type="integer", nullable=true)
     */
    private $settlementId;


    /**
     * @return Planet
     */
    public function getSettlementPlanet()
    {
        return $this->settlementPlanet;
    }

    /**
     * @param Planet $settlementPlanet
     */
    public function setSettlementPlanet($settlementPlanet)
    {
        $this->settlementPlanet = $settlementPlanet;
    }

    /**
     * @return int
     */
    public function getSettlementId()
    {
        return $this->settlementId;
    }

    /**
     * @param int $settlementId
     */
    public function setSettlementId($settlementId)
    {
        $this->settlementId = $settlementId;
    }
}