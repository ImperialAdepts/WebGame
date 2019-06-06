<?php
namespace AppBundle\Entity\Human;

use AppBundle\Entity\Human;
use AppBundle\Entity\SolarSystem\Planet;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 * @ORM\Table(name="human_titles")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Human\TitleRepository")
 */
class Title
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var Human
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Human")
     * @ORM\JoinColumn(name="human_holder_id", referencedColumnName="id", nullable=true)
     */
    private $humanHolder;

    /**
     * @var Title
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Human\Title")
     * @ORM\JoinColumn(name="superior_title_id", referencedColumnName="id", nullable=true)
     */
    private $superiorTitle;

    /**
     * @var string[] string => string
     *
     * @ORM\Column(name="inheritance_settings", type="json_array", nullable=true)
     */
    private $transferSettings;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return Human
     */
    public function getHumanHolder()
    {
        return $this->humanHolder;
    }

    /**
     * @param Human $humanHolder
     */
    public function setHumanHolder($humanHolder)
    {
        $this->humanHolder = $humanHolder;
    }

    /**
     * @return Title
     */
    public function getSuperiorTitle()
    {
        return $this->superiorTitle;
    }

    /**
     * @param Title $superiorTitle
     */
    public function setSuperiorTitle($superiorTitle)
    {
        $this->superiorTitle = $superiorTitle;
    }


    /**
     * @return string[]
     */
    public function getTransferSettings()
    {
        return $this->transferSettings;
    }

    /**
     * @param string[] $transferSettings
     */
    public function setTransferSettings($transferSettings)
    {
        $this->transferSettings = $transferSettings;
    }
}