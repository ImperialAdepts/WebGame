<?php
namespace AppBundle\Descriptor\Adapters;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\ResourcefullInterface;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Descriptor\UseCaseTraitEnum;
use AppBundle\Entity; use PlanetBundle\Entity as PlanetEntity;

class People extends AbstractResourceDepositAdapter
{
    /**
     * @param ResourcefullInterface $resourcefull
     * @return People[]
     */
    public static function in(ResourcefullInterface $resourcefull) {
        return parent::extractAdapterOfUseCase($resourcefull, UseCaseEnum::PEOPLE);
    }

    /**
     * @param PlanetEntity\Region $region
     * @param string $resourceDescriptor
     * @return People
     */
    public static function findByDescriptor(PlanetEntity\Region $region, $resourceDescriptor) {
        $descriptor = $region->getResourceDeposit($resourceDescriptor);
        if ($descriptor == null) return null;
        return $descriptor->asUseCase(UseCaseEnum::PEOPLE);
    }


    /**
     * @param People[] $peoples
     * @return int
     */
    public static function countPeople(array $peoples) {
        $peopleCount = 0;
        /** @var People $people */
        foreach ($peoples as $people) {
            if ($people instanceof People) {
                $peopleCount += $people->getPeopleCount();
            }
        }
        return $peopleCount;
    }

    public function getPeopleCount() {
        if ($this->getBlueprint() != null) {
            $price = $this->getBlueprint()->getResourceRequirements();
            if (!isset($price[ResourceDescriptorEnum::PEOPLE])) return 0;
            return $this->getDeposit()->getAmount() * $price[ResourceDescriptorEnum::PEOPLE];
        } elseif ($this->getResourceDescriptor() == ResourceDescriptorEnum::PEOPLE) {
            return $this->getDeposit()->getAmount();
        }
    }

    /**
     * @return int Joule
     */
    public function getFoodEnergyConsumptionPerHuman() {
        return 3500000;
    }

    /**
     * @param People[] $peoples
     * @return int Joule
     */
    public static function countFoodEnergyConsumption(array $peoples) {
        $energyCount = 0;
        /** @var People $people */
        foreach ($peoples as $people) {
            if ($people instanceof People) {
                $energyCount += $people->getFoodEnergyConsumption();
            }
        }
        return $energyCount;
    }

    /**
     * @return int Joule
     */
    public function getFoodEnergyConsumption() {
        return $this->getPeopleCount()*$this->getFoodEnergyConsumptionPerHuman();
    }

    /**
     * @return float
     */
    public function getFertilityRate()
    {
        return 2;
    }

}