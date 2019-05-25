<?php

namespace AppBundle\Entity\rpg;

use AppBundle\Entity\Human;
use AppBundle\Entity\Soul;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Table(name="human_preference", indexes={@ORM\Index(name="humans_preferences_index", columns={"type", "human_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\rpg\HumanPreferenceRepository")
 */
class HumanPreference
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
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
     * @var string
     *
     * @ORM\Column(name="value", type="string", nullable=false)
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Human")
     * @ORM\JoinColumn(name="human_id", referencedColumnName="id", nullable=false)
     */
    private $human;

    /**
     * @param $type
     * @param Human $human
     * @return HumanPreference
     */
    public static function create($type, $value, Human $human) {
        $pref = new self();
        $pref->setType($type);
        $pref->setValue($value);
        $pref->setHuman($human);
        return $pref;
    }

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
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }


    /**
     * @return Human
     */
    public function getHuman()
    {
        return $this->human;
    }

    /**
     * @param Human $human
     */
    public function setHuman(Human $human)
    {
        $this->human = $human;
    }

}

