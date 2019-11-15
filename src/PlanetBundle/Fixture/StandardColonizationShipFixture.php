<?php
namespace PlanetBundle\Fixture;

use AppBundle\Entity as GeneralEntity;
use AppBundle\Fixture\PlanetsFixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\ORM\EntityManager;
use PlanetBundle\Builder\RegionTerrainTypeEnumBuilder;
use PlanetBundle\Concept;
use PlanetBundle\Entity as PlanetEntity;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use PlanetBundle\UseCase\TeamBuilders;
use PlanetBundle\UseCase\TeamFarmers;
use PlanetBundle\UseCase\TeamScientists;
use PlanetBundle\UseCase\TeamWorkers;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tracy\Debugger;

class StandardColonizationShipFixture extends \Doctrine\Bundle\FixturesBundle\Fixture implements ContainerAwareInterface, DependentFixtureInterface
{
    const DEPOSIT_CODE = "standard-colonization-pack";
    /**
     * The dependency injection container.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $generalManager
     * @throws \Exception
     */
	public function load(\Doctrine\Common\Persistence\ObjectManager $generalManager)
	{
        echo __CLASS__."\n";

        /** @var GeneralEntity\SolarSystem\Planet $planets */
        $planets = $generalManager->getRepository(GeneralEntity\SolarSystem\Planet::class)->findAll();
        /** @var GeneralEntity\SolarSystem\Planet $planet */
        foreach ($planets as $planet) {
            echo $planet->getName() . "\n";
            if ($planet->getDatabaseCredentials() == null) {
                echo "skipped\n";
                continue;
            }

            $this->container->get('dynamic_planet_connector')->setPlanet($planet, true);
            $manager = $this->container->get('doctrine')->getManager('planet');

            $deposit = new PlanetEntity\Resource\StandardizedDeposit(self::DEPOSIT_CODE);
            $this->fillDeposit($deposit, $manager);

            $manager->persist($deposit);
            $this->setReference(self::DEPOSIT_CODE.$planet->getId(), $deposit);
            $manager->flush();

            echo "done\n";
        }
	}

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }


    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
           PlanetsFixture::class,
        ];
    }

    /**
     * @param PlanetEntity\Resource\StandardizedDeposit $deposit
     * @return PlanetEntity\Resource\BlueprintRecipe[]
     */
    private function fillDeposit(PlanetEntity\Resource\StandardizedDeposit $deposit, EntityManager $entityManager)
    {
        $human = new Concept\People();
        $deposit->addResourceDescriptors($this->newThings(100, $humanBlueprint = $human->getBlueprint("Earthling")));
        $entityManager->persist($humanBlueprint); $entityManager->flush();
        {
            $sex = new PlanetEntity\Resource\BlueprintRecipe(new PlanetEntity\Resource\Thing($humanBlueprint, 1));
            $sex->setDescription("Sexual reproduction");
            $sex->setTools(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($humanBlueprint, 365 * 24, 2), // clovekorok prace
            ]));
        }
        $teamFarmers = new Concept\Team\Farmers();
        $teamFarmers->setPeopleCount(5);
        $deposit->addResourceDescriptors($this->newThings(1, $farmerBlueprint = $teamFarmers->getBlueprint("Small farmers")));
        $entityManager->persist($farmerBlueprint); $entityManager->flush();
        {
            $createTeam = new PlanetEntity\Resource\BlueprintRecipe(new PlanetEntity\Resource\Thing($farmerBlueprint, 1));
            $createTeam->setDescription("Organize farmers");
            $createTeam->setInputs(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($humanBlueprint, 5),
            ]));
        }
        $teamWorkers = new Concept\Team\Workers();
        $teamWorkers->setPeopleCount(5);
        $deposit->addResourceDescriptors($this->newThings(1, $workerBlueprint = $teamWorkers->getBlueprint("Worker pack")));
        $entityManager->persist($workerBlueprint); $entityManager->flush();
        {
            $createTeam = new PlanetEntity\Resource\BlueprintRecipe(new PlanetEntity\Resource\Thing($workerBlueprint, 1));
            $createTeam->setDescription("Organize workers");
            $createTeam->setInputs(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($humanBlueprint, 5),
            ]));
        }
        $merchants = new Concept\Team\Merchants();
        $merchants->setPeopleCount(5);
        $deposit->addResourceDescriptors($this->newThings(1, $merchantTeamBlueprint = $merchants->getBlueprint("Merchant caravan")));
        $entityManager->persist($merchantTeamBlueprint); $entityManager->flush();
        {
            $createTeam = new PlanetEntity\Resource\BlueprintRecipe(new PlanetEntity\Resource\Thing($merchantTeamBlueprint, 1));
            $createTeam->setDescription("Organize merchants");
            $createTeam->setInputs(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($humanBlueprint, 5),
            ]));
        }
        $scientists = new Concept\Team\Scientists();
        $scientists->setPeopleCount(5);
        $deposit->addResourceDescriptors($this->newThings(1, $scientistTeamBlueprint = $scientists->getBlueprint("Scientist")));
        $entityManager->persist($scientistTeamBlueprint); $entityManager->flush();
        {
            $createTeam = new PlanetEntity\Resource\BlueprintRecipe(new PlanetEntity\Resource\Thing($scientistTeamBlueprint, 1));
            $createTeam->setDescription("Organize scientists");
            $createTeam->setInputs(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($humanBlueprint, 5),
            ]));
        }
        $soldiers = new Concept\Team\Soldiers();
        $soldiers->setPeopleCount(50);
        $deposit->addResourceDescriptors($this->newThings(1, $soldienTeamBlueprint = $soldiers->getBlueprint("Army")));
        $entityManager->persist($soldienTeamBlueprint); $entityManager->flush();
        {
            $createTeam = new PlanetEntity\Resource\BlueprintRecipe(new PlanetEntity\Resource\Thing($soldienTeamBlueprint, 1));
            $createTeam->setDescription("Organize army");
            $createTeam->setInputs(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($humanBlueprint, 5),
            ]));
        }
        $transporters = new Concept\Team\Transporters();
        $transporters->setPeopleCount(10);
        $deposit->addResourceDescriptors($this->newThings(1, $transporterTeamBlueprint = $transporters->getBlueprint("Logistic group")));
        $entityManager->persist($transporterTeamBlueprint); $entityManager->flush();
        {
            $createTeam = new PlanetEntity\Resource\BlueprintRecipe(new PlanetEntity\Resource\Thing($transporterTeamBlueprint, 1));
            $createTeam->setDescription("Organize transports");
            $createTeam->setInputs(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($humanBlueprint, 5),
            ]));
        }
        $builders = new Concept\Team\Builders();
        $builders->setPeopleCount(25);
        $deposit->addResourceDescriptors($this->newThings(1, $builderTeamBlueprint = $builders->getBlueprint("Builder team")));
        $entityManager->persist($builderTeamBlueprint); $entityManager->flush();
        {
            $createTeam = new PlanetEntity\Resource\BlueprintRecipe(new PlanetEntity\Resource\Thing($builderTeamBlueprint, 1));
            $createTeam->setDescription("Organize builders");
            $createTeam->setInputs(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($humanBlueprint, 25),
            ]));
        }
        {
            $cloning = new PlanetEntity\Resource\BlueprintRecipe(new PlanetEntity\Resource\Thing($humanBlueprint, 100));
            $cloning->setDescription("Cloning people");
            $cloning->setTools(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($humanBlueprint, 1),
                new PlanetEntity\Resource\Thing($scientistTeamBlueprint, 365*2),
            ]));
        }

        $ironOre = new Concept\MetalOre();
        $ironOre->setQuality(1);
        $ironOre->setWeight(10);
        $deposit->addResourceDescriptors($this->newThings(100, $ironOreBlueprint = $ironOre->getBlueprint("Iron ore")));
        $entityManager->persist($ironOreBlueprint); $entityManager->flush();
        {
            $ironMining = new PlanetEntity\Resource\BlueprintRecipe(new PlanetEntity\Resource\Thing($ironOreBlueprint, 1));
            $ironMining->setDescription("Mining");
            $ironMining->setTools(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($workerBlueprint, 20),
            ]));
        }

        $ironPlate = new Concept\MetalPlate();
        $ironPlate->setWeight(3);
        $ironPlate->setMetalType(PlanetEntity\Resource\MetalTypeEnum::IRON);
        $ironPlate->setThickness(3);
        $ironPlate->setWeightPerM2(100);
        $deposit->addResourceDescriptors($this->newThings(100, $ironPlateBlueprint = $ironPlate->getBlueprint("Iron plate")));
        $entityManager->persist($ironPlateBlueprint); $entityManager->flush();
        {
            $plating = new PlanetEntity\Resource\BlueprintRecipe(new PlanetEntity\Resource\Thing($ironPlateBlueprint, 100));
            $plating->setDescription("Plating");
            $plating->setTools(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($workerBlueprint, 10),
            ]));
            $plating->setInputs(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($ironOreBlueprint, 120),
            ]));
        }
        $ironTraverse = new Concept\MetalTraverse();
        $ironTraverse->setWeight(3);
        $ironTraverse->setMetalType(PlanetEntity\Resource\MetalTypeEnum::IRON);
        $ironTraverse->setThickness(3);
        $ironTraverse->setWeightPerM2(300);
        $deposit->addResourceDescriptors($this->newThings(1, $ironTraverseBlueprint = $ironTraverse->getBlueprint("Iron Traverse")));
        $entityManager->persist($ironTraverseBlueprint); $entityManager->flush();
        {
            $traversing = new PlanetEntity\Resource\BlueprintRecipe(new PlanetEntity\Resource\Thing($ironTraverseBlueprint, 100));
            $traversing->setDescription("Traversing");
            $traversing->setInputs(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($ironOreBlueprint, 120),
            ]));
            $traversing->setTools(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($workerBlueprint, 10),
            ]));
        }

        $farm = new Concept\Farm();
        $farm->setSpace(500);
        $deposit->addResourceDescriptors($this->newThings(0, $farmBlueprint = $farm->getBlueprint("Farm")));
        $entityManager->persist($farmBlueprint); $entityManager->flush();
        {
            $makeFarm = new PlanetEntity\Resource\BlueprintRecipe(new PlanetEntity\Resource\Thing($farmBlueprint, 1));
            $makeFarm->setDescription("Settle farm");
            $makeFarm->setTools(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($farmerBlueprint, 1),
            ]));
        }

        $wheet = new Concept\Food();
        $wheet->setEnergy(8000);
        $deposit->addResourceDescriptors($this->newThings(5000, $wb = $wheet->getBlueprint("Wheet")));
        $entityManager->persist($wb); $entityManager->flush();
        {
            $smallFarming = new PlanetEntity\Resource\BlueprintRecipe(new PlanetEntity\Resource\Thing($wb, 20));
            $smallFarming->setDescription("Carefull farming");
            $smallFarming->setTools(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($farmerBlueprint, 5),
                new PlanetEntity\Resource\Thing($farmBlueprint, 1),
            ]));
            $smallFarming->setInputs(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($wb, 1),
            ]));

            $bigFarming = new PlanetEntity\Resource\BlueprintRecipe(new PlanetEntity\Resource\Thing($wb, 15000));
            $bigFarming->setDescription("Agro industry");
            $bigFarming->setTools(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($farmerBlueprint, 500),
                new PlanetEntity\Resource\Thing($farmBlueprint, 1),
            ]));
            $bigFarming->setInputs(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($wb, 1000),
            ]));
        }

        $container = new Concept\Container();
        $container->setArea(60);
        $container->setWeight(1000);
        $deposit->addResourceDescriptors($this->newThings(5000, $containerBlueprint = $container->getBlueprint("Generic container")));
        $entityManager->persist($containerBlueprint); $entityManager->flush();
        {
            $creation = new PlanetEntity\Resource\BlueprintRecipe(new PlanetEntity\Resource\Thing($containerBlueprint, 1));
            $creation->setDescription("Container creation");
            $creation->setTools(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($workerBlueprint, 10),
            ]));
            $creation->setInputs(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($ironPlateBlueprint, 4),
            ]));
        }

        $containerWarehouse = new Concept\Warehouse();
        $containerWarehouse->setSkeleton($container);
        $containerWarehouse->setArea(60);
        $containerWarehouse->setSpaceCapacity(350);
        $containerWarehouse->setWeightCapacity(10000);
        $deposit->addResourceDescriptors($this->newThings(3, $containerWarehouse = $containerWarehouse->getBlueprint("Container warehouse")));
        $entityManager->persist($containerWarehouse); $entityManager->flush();
        {
            $place = new PlanetEntity\Resource\BlueprintRecipe(new PlanetEntity\Resource\Thing($containerWarehouse, 1));
            $place->setDescription("Make warehouse from container");
            $place->setTools(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($builderTeamBlueprint, 10),
            ]));
            $place->setInputs(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($containerBlueprint),
            ]));
        }

        $containerHouse = new Concept\House();
        $containerHouse->setArea(60);
        $containerHouse->setPeopleCapacity(3);
        $containerHouse->setPeopleMaxCapacity(30);
        $deposit->addResourceDescriptors($this->newThings(10, $containerHouse = $containerHouse->getBlueprint("Container house")));
        $entityManager->persist($containerHouse); $entityManager->flush();
        {
            $place = new PlanetEntity\Resource\BlueprintRecipe(new PlanetEntity\Resource\Thing($containerHouse, 1));
            $place->setDescription("Make house from container");
            $place->setInputs(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($containerBlueprint),
            ]));
            $place->setTools(new PlanetEntity\Resource\BlueprintRecipeDeposit([
                new PlanetEntity\Resource\Thing($builderTeamBlueprint, 10),
            ]));
        }
    }

    private function newThings($count, PlanetEntity\Resource\Blueprint $blueprint) {
        return new PlanetEntity\Resource\Thing($blueprint, $count);
    }

}