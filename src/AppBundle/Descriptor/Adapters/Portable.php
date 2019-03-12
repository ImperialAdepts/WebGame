<?php
namespace AppBundle\Descriptor\Adapters;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Descriptor\UseCaseTraitEnum;
use AppBundle\Entity;

class Portable extends AbstractResourceDepositAdapter
{
    /**
     * @param ResourcefullInterface $resourcefull
     * @return Portable[]
     */
    public static function in(ResourcefullInterface $resourcefull) {
        return parent::extractAdapterOfUseCase($resourcefull, UseCaseEnum::PORTABLES);
    }


    /**
     * @param Portable[] $portables
     * @return int
     */
    public static function countWeight(array $portables) {
        $people = 0;
        /** @var Portable $team */
        foreach ($portables as $team) {
            if ($team instanceof Portable) {
                $people += $team->getWeight();
            }
        }
        return $people;
    }

    public function getWeight() {
        return $this->getDeposit()->getAmount()*$this->getWeightPerUnit();
    }

    public function getWeightPerUnit() {
        return $this->getBlueprint()->getTraitValue(UseCaseTraitEnum::WEIGHT, 0);;
    }
}