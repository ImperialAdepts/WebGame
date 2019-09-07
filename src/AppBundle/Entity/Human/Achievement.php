<?php
namespace AppBundle\Entity\Human;

use AppBundle\Entity\Human;
use AppBundle\Entity\PlanetAndPhaseTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="human_achievements")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Human\AchievementRepository")
 */
class Achievement
{
    use PlanetAndPhaseTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string pozdeji predelat jako enum
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;

    /**
     * @var string pozdeji predelat jako enum
     *
     * @ORM\Column(name="time_type", type="string", nullable=false)
     */
    private $timeType;

    /**
     * @var string pozdeji predelat jako enum
     *
     * @ORM\Column(name="space_type", type="string", nullable=false)
     */
    private $spaceType;

    /**
     * @var Human
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Human", inversedBy="achievements")
     * @ORM\JoinColumn(name="human_holder_id", referencedColumnName="id", nullable=false)
     */
    private $holder;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTimeType()
    {
        return $this->timeType;
    }

    /**
     * @param string $timeType
     */
    public function setTimeType($timeType)
    {
        $this->timeType = $timeType;
    }

    /**
     * @return string
     */
    public function getSpaceType()
    {
        return $this->spaceType;
    }

    /**
     * @param string $spaceType
     */
    public function setSpaceType($spaceType)
    {
        $this->spaceType = $spaceType;
    }

    /**
     * @return Human
     */
    public function getHolder()
    {
        return $this->holder;
    }

    /**
     * @param Human $holder
     */
    public function setHolder($holder)
    {
        $this->holder = $holder;
    }

}