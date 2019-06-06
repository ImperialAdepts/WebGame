<?php
namespace AppBundle\Entity\Human;

use AppBundle\Entity\Human;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="human_title_delegations")
 * @ORM\Entity()
 * repositoryClass="AppBundle\Repository\Human\TitleRepository")
 */
class CompetenceDelegation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Title
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Human\Title")
     * @ORM\JoinColumn(name="delegating_title_id", referencedColumnName="id", nullable=true)
     */
    private $delegatingTitle;

    /**
     * @var Title
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Human\Title")
     * @ORM\JoinColumn(name="executive_title_id", referencedColumnName="id", nullable=true)
     */
    private $executiveTitle;

    /**
     * @var Human
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Human")
     * @ORM\JoinColumn(name="human_creator_id", referencedColumnName="id", nullable=true)
     */
    private $humanCreator;

    /**
     * @var string
     * TODO: predelat na enum
     *
     * @ORM\Column(name="competence", type="string", nullable=false)
     */
    private $competence;

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
     * @return Title
     */
    public function getDelegatingTitle()
    {
        return $this->delegatingTitle;
    }

    /**
     * @param Title $delegatingTitle
     */
    public function setDelegatingTitle($delegatingTitle)
    {
        $this->delegatingTitle = $delegatingTitle;
    }

    /**
     * @return Title
     */
    public function getExecutiveTitle()
    {
        return $this->executiveTitle;
    }

    /**
     * @param Title $executiveTitle
     */
    public function setExecutiveTitle($executiveTitle)
    {
        $this->executiveTitle = $executiveTitle;
    }

    /**
     * @return Human
     */
    public function getHumanCreator()
    {
        return $this->humanCreator;
    }

    /**
     * @param Human $humanCreator
     */
    public function setHumanCreator($humanCreator)
    {
        $this->humanCreator = $humanCreator;
    }

    /**
     * @return string
     */
    public function getCompetence()
    {
        return $this->competence;
    }

    /**
     * @param string $competence
     */
    public function setCompetence($competence)
    {
        $this->competence = $competence;
    }

}