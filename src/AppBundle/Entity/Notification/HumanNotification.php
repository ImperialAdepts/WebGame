<?php

namespace AppBundle\Entity\Notification;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * HumanNotification
 *
 * @ORM\Table(name="notifications_human")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NotificationRepository")
 */
class HumanNotification extends GenericNotification
{
    /**
     * @var Human
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Human")
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

