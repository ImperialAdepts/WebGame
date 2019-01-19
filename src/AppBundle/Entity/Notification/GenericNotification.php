<?php

namespace AppBundle\Entity\Notification;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * GenericNotification
 *
 * @ ORM\Table(name="notifications")
 * @ ORM\Entity(repositoryClass="AppBundle\Repository\NotificationRepository")
 * @ORM\MappedSuperclass
 */
class GenericNotification
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
	 * @var string
	 *
	 * @ORM\Column(name="description", type="string", length=255)
	 */
	private $description;

    /**
     * @var Gamer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Gamer")
     * @ORM\JoinColumn(fieldName="gamer_id", referencedColumnName="id", nullable=true)
     */
	private $gamer;

    /**
     * @var int
     *
     * @ORM\Column(name="creation_time", type="integer")
     */
	private $creationTime;


	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set name
	 *
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
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
    public function setGamer(Gamer $gamer)
    {
        $this->gamer = $gamer;
    }

    /**
     * @return int
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @param int $creationTime
     */
    public function setCreationTime($creationTime)
    {
        $this->creationTime = $creationTime;
    }


}

