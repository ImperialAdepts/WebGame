<?php
namespace AppBundle;

use AppBundle\Repository\HumanRepository;
use AppBundle\Entity;
use Symfony\Component\HttpFoundation\Session\Session;

class LoggedUserSettings
{
    /** @var Session */
    private $session;
    /** @var HumanRepository */
    private $humanRepository;
    /** @var Entity\Human */
    private $human;

    /**
     * LoggedUserSettings constructor.
     * @param Session $session
     * @param HumanRepository $humanRepository
     */
    public function __construct(Session $session, HumanRepository $humanRepository)
    {
        $this->session = $session;
        $this->humanRepository = $humanRepository;
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


}