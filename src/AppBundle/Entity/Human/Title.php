<?php
namespace AppBundle\Entity\Human;

use AppBundle\Entity\Human;
use AppBundle\Entity\SolarSystem\Planet;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"general" = "Title", "land-title" = "SettlementTitle"})
 * @ORM\Table(name="human_titles")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Human\TitleRepository")
 */
class Title
{
    const LINE_OF_SUCCESSION_MAX_SIZE = 100;

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

    /**
     * @return Human|null
     */
    public function getHeir() {
        $line = $this->getLineOfSuccession();
        return array_pop($line);
    }

    /**
     * @return Human[]
     */
    public function getLineOfSuccession() {
        $line = [];
//        if (isset($transferSettings['inheritance'])) {
            $this->addChildren($line, $this->humanHolder);
//        }
        if (count($line) >= self::LINE_OF_SUCCESSION_MAX_SIZE) {
            return $line;
        }
        if ($this->getSuperiorTitle() != null && count($line) <= self::LINE_OF_SUCCESSION_MAX_SIZE) {
            $line[] = $this->getSuperiorTitle()->getHumanHolder();
            foreach ($this->getSuperiorTitle()->getLineOfSuccession() as $superiorSuccessor) {
                if (count($line) >= self::LINE_OF_SUCCESSION_MAX_SIZE) {
                    return $line;
                }
                if ($superiorSuccessor->getDeathTime() === null) {
                    $line[] = $superiorSuccessor;
                }
            }
        }
        return $line;
    }

    private function addChildren(array &$line, Human $human) {
        if (count($line) > self::LINE_OF_SUCCESSION_MAX_SIZE) {
            return;
        }
        foreach ($human->getChildren() as $child) {
            if ($child->getDeathTime() === null) {
                $line[] = $child;
            }
        }
        foreach ($human->getChildren() as $child) {
            $this->addChildren($line, $child);
        }
    }
}