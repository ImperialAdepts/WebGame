<?php

namespace AppBundle\Maintainer;

use AppBundle\Descriptor\Adapters\BasicFood;
use AppBundle\Descriptor\Adapters\Team;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Entity\Planet\Region;
use AppBundle\Entity\ResourceDeposit;
use Doctrine\ORM\EntityManager;

class Maintainer
{
    /** @var EntityManager */
    private $entityManager;

    /**
     * Maintainer constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function clearEmptyDeposits() {
        $emptyDeposits = $this->entityManager->getRepository(ResourceDeposit::class)->getEmpty();
        foreach ($emptyDeposits as $deposit) {
            $this->entityManager->remove($deposit);
        }
        $this->entityManager->flush();
    }
}