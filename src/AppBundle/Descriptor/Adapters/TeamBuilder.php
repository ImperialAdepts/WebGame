<?php
namespace AppBundle\Descriptor\Adapters;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Descriptor\UseCaseTraitEnum;
use AppBundle\Entity;

class TeamBuilder extends AbstractResourceDepositAdapter
{
    /**
     * @param ResourcefullInterface $resourcefull
     * @return Team[]
     */
    public static function in(ResourcefullInterface $resourcefull) {
        return parent::extractAdapterOfUseCase($resourcefull, UseCaseEnum::TEAM_BUILDERS);
    }


    /**
     * @param Team[] $teams
     * @return int
     */
    public static function countPeople(array $teams) {
        $people = 0;
        /** @var Team $team */
        foreach ($teams as $team) {
            if ($team instanceof Team) {
                $people += $team->getPeopleCount();
            }
        }
        return $people;
    }

    public function getPeopleCount() {
        $price = $this->getBlueprint()->getRequirements();
        if (!isset($price[ResourceDescriptorEnum::PEOPLE])) return 0;
        return $this->getDeposit()->getAmount()*$price[ResourceDescriptorEnum::PEOPLE];
    }
}