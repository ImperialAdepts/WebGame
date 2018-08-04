<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Soul
 *
 * @ORM\Table(name="souls", indexes={@ORM\Index(name="souls_gamers_FK", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SoulRepository")
 */
class Soul
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var Gamer
     *
     * @ORM\ManyToOne(targetEntity="Gamer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $gamer;

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
     * @return Gamer
     */
    public function getGamer()
    {
        return $this->gamer;
    }

    /**
     * @param Gamer $gamer
     */
    public function setGamer($gamer)
    {
        $this->gamer = $gamer;
    }


}

