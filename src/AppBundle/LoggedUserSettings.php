<?php
namespace AppBundle;

use AppBundle\Repository\GamerRepository;
use AppBundle\Repository\HumanRepository;
use AppBundle\Entity;
use AppBundle\Repository\SolarSystem\PlanetRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use Tracy\Debugger;

class LoggedUserSettings
{
    /** @var Session */
    private $session;
    /** @var HumanRepository */
    private $humanRepository;
    /** @var GamerRepository */
    private $gamerRepository;
    /** @var PlanetRepository */
    private $planetRepository;
    /** @var Entity\Human */
    private $human;
    /** @var Entity\Gamer */
    private $gamer;
    /** @var Entity\SolarSystem\Planet */
    private $planet;

    /**
     * LoggedUserSettings constructor.
     * @param Session $session
     * @param HumanRepository $humanRepository
     * @param GamerRepository $gamerRepository
     * @param PlanetRepository $planetRepository
     */
    public function __construct(Session $session, HumanRepository $humanRepository, GamerRepository $gamerRepository, PlanetRepository $planetRepository)
    {
        $this->session = $session;
        $this->humanRepository = $humanRepository;
        $this->gamerRepository = $gamerRepository;
        $this->planetRepository = $planetRepository;
    }

    /**
     * @return Entity\Human
     */
    public function getHuman()
    {
        if ($this->human == null && ($humanId = $this->session->get('human_id')) != null) {
            $this->human = $this->humanRepository->find($humanId);
        }
        return $this->human;
    }

    /**
     * @param Entity\Human $human
     */
    public function setHuman(Entity\Human $human)
    {
        $this->human = $human;
        $this->session->set('human_id', $human->getId());
    }

    /**
     * @return Entity\Gamer
     */
    public function getGamer()
    {
        if ($this->gamer == null && ($gamerId = $this->session->get('gamer_id')) != null) {
            $this->gamer = $this->gamerRepository->find($gamerId);
        }
        return $this->gamer;
    }

    /**
     * @param Entity\Gamer $gamer
     */
    public function setGamer(Entity\Gamer $gamer)
    {
        $this->gamer = $gamer;
        $this->session->set('gamer_id', $gamer->getId());
    }

    /**
     * @return Entity\SolarSystem\Planet
     */
    public function getPlanet()
    {
        if ($this->planet == null && ($planetId = $this->session->get('planet_id')) != null) {
            $this->planet = $this->planetRepository->find($planetId);
        }
        return $this->planet;
    }

    /**
     * @param Entity\SolarSystem\Planet $planet
     */
    public function setPlanet(Entity\SolarSystem\Planet $planet)
    {
        $this->planet = $planet;
        $this->session->set('planet_id', $planet->getId());
    }
}