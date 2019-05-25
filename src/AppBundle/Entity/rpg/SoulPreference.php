<?php

namespace AppBundle\Entity\rpg;

use AppBundle\Entity\Soul;
use Doctrine\ORM\Mapping as ORM;
/**
 * Planet
 *
 * @ORM\Table(name="soul_preference", indexes={@ORM\Index(name="souls_preferences_index", columns={"type", "soul_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\rpg\SoulPreferenceRepository")
 */
class SoulPreference
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Soul")
     * @ORM\JoinColumn(name="soul_id", referencedColumnName="id", nullable=false)
     */
    private $soul;

    /**
     * @param $type
     * @param Soul $soul
     * @return SoulPreference
     */
    public static function create($type, Soul $soul) {
        $pref = new self();
        $pref->setType($type);
        $pref->setSoul($soul);
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
     * @return Soul
     */
    public function getSoul()
    {
        return $this->soul;
    }

    /**
     * @param Soul $soul
     */
    public function setSoul(Soul $soul)
    {
        $this->soul = $soul;
    }

}

