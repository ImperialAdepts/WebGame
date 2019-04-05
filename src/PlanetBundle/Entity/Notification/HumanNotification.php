<?php

namespace PlanetBundle\Entity\Notification;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * HumanNotification
 *
 * @ORM\Table(name="notifications_human")
 * @ORM\Entity(repositoryClass="PlanetBundle\Repository\NotificationRepository")
 */
class HumanNotification extends GenericNotification
{
    /**
     * @var Human
     *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Human")
     * @ORM\JoinColumn(fieldName="human_id", referencedColumnName="id", nullable=false)
     */
	private $human;

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

