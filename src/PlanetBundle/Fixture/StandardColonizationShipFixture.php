<?php
namespace PlanetBundle\Fixture;

use AppBundle\Entity as GeneralEntity;
use AppBundle\Fixture\PlanetsFixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
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
            $this->fillDeposit($deposit);

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
    private function fillDeposit(PlanetEntity\Resource\StandardizedDeposit $deposit)
    {
        $human = new Concept\People();
        $deposit->addResourceDescriptors($this->newThings(100, $humanBlueprint = $human->getBlueprint("Earthling")));
        {
            $sex = new PlanetEntity\Resource\BlueprintRecipe($humanBlueprint);
            $sex->setDescription("Sexual reproduction");
            $sex->addTool($humanBlueprint, 365 * 24, 2); // clovekorok prace
        }
        $teamFarmers = new Concept\Team\Farmers();
        $teamFarmers->setPeopleCount(5);
        $deposit->addResourceDescriptors($this->newThings(1, $farmerBlueprint = $teamFarmers->getBlueprint("Small farmers")));
        {
            $createTeam = new PlanetEntity\Resource\BlueprintRecipe($farmerBlueprint);
            $createTeam->setDescription("Organize farmers");
            $createTeam->addInputBlueprint($humanBlueprint, 5);
        }
        $teamWorkers = new Concept\Team\Workers();
        $teamWorkers->setPeopleCount(5);
        $deposit->addResourceDescriptors($this->newThings(1, $workerBlueprint = $teamWorkers->getBlueprint("Worker pack")));
        {
            $createTeam = new PlanetEntity\Resource\BlueprintRecipe($workerBlueprint);
            $createTeam->setDescription("Organize workers");
            $createTeam->addInputBlueprint($humanBlueprint, 5);
        }
        $merchants = new Concept\Team\Merchants();
        $merchants->setPeopleCount(5);
        $deposit->addResourceDescriptors($this->newThings(1, $merchantTeamBlueprint = $merchants->getBlueprint("Merchant caravan")));
        {
            $createTeam = new PlanetEntity\Resource\BlueprintRecipe($merchantTeamBlueprint);
            $createTeam->setDescription("Organize merchants");
            $createTeam->addInputBlueprint($humanBlueprint, 5);
        }
        $scientists = new Concept\Team\Scientists();
        $scientists->setPeopleCount(5);
        $deposit->addResourceDescriptors($this->newThings(1, $scientistTeamBlueprint = $scientists->getBlueprint("Scientist")));
        {
            $createTeam = new PlanetEntity\Resource\BlueprintRecipe($scientistTeamBlueprint);
            $createTeam->setDescription("Organize scientists");
            $createTeam->addInputBlueprint($humanBlueprint, 5);
        }
        $soldiers = new Concept\Team\Soldiers();
        $soldiers->setPeopleCount(50);
        $deposit->addResourceDescriptors($this->newThings(1, $soldienTeamBlueprint = $soldiers->getBlueprint("Army")));
        {
            $createTeam = new PlanetEntity\Resource\BlueprintRecipe($soldienTeamBlueprint);
            $createTeam->setDescription("Organize army");
            $createTeam->addInputBlueprint($humanBlueprint, 5);
        }
        $transporters = new Concept\Team\Transporters();
        $transporters->setPeopleCount(10);
        $deposit->addResourceDescriptors($this->newThings(1, $transporterTeamBlueprint = $transporters->getBlueprint("Logistic group")));
        {
            $createTeam = new PlanetEntity\Resource\BlueprintRecipe($transporterTeamBlueprint);
            $createTeam->setDescription("Organize transports");
            $createTeam->addInputBlueprint($humanBlueprint, 5);
        }
        $builders = new Concept\Team\Builders();
        $builders->setPeopleCount(25);
        $deposit->addResourceDescriptors($this->newThings(1, $builderTeamBlueprint = $builders->getBlueprint("Builder team")));
        {
            $createTeam = new PlanetEntity\Resource\BlueprintRecipe($builderTeamBlueprint);
            $createTeam->setDescription("Organize builders");
            $createTeam->addInputBlueprint($humanBlueprint, 25);
        }
        {
            $cloning = new PlanetEntity\Resource\BlueprintRecipe($humanBlueprint);
            $cloning->setDescription("Cloning people");
            $cloning->setMainProductCount(100);
            $cloning->addTool($humanBlueprint, 1, 1);
            $cloning->addTool($scientistTeamBlueprint, 365*2, 2);
        }

        $ironOre = new Concept\MetalOre();
        $ironOre->setQuality(1);
        $ironOre->setWeight(10);
        $deposit->addResourceDescriptors($this->newThings(100, $ironOreBlueprint = $ironOre->getBlueprint("Iron ore")));
        {
            $ironMining = new PlanetEntity\Resource\BlueprintRecipe($ironOreBlueprint);
            $ironMining->setDescription("Mining");
            $ironMining->addTool($workerBlueprint, 20);
        }

        $ironPlate = new Concept\MetalPlate();
        $ironPlate->setWeight(3);
        $ironPlate->setMetalType(PlanetEntity\Resource\MetalTypeEnum::IRON);
        $ironPlate->setThickness(3);
        $ironPlate->setWeightPerM2(100);
        $deposit->addResourceDescriptors($this->newThings(100, $ironPlateBlueprint = $ironPlate->getBlueprint("Iron plate")));
        {
            $plating = new PlanetEntity\Resource\BlueprintRecipe($ironPlateBlueprint);
            $plating->setMainProductCount(100);
            $plating->setDescription("Plating");
            $plating->addTool($workerBlueprint, 10);
            $plating->addInputBlueprint($ironOreBlueprint, 120);
        }
        $ironTraverse = new Concept\MetalTraverse();
        $ironTraverse->setWeight(3);
        $ironTraverse->setMetalType(PlanetEntity\Resource\MetalTypeEnum::IRON);
        $ironTraverse->setThickness(3);
        $ironTraverse->setWeightPerM2(300);
        $deposit->addResourceDescriptors($this->newThings(1, $ironTraverseBlueprint = $ironTraverse->getBlueprint("Iron Traverse")));
        {
            $traversing = new PlanetEntity\Resource\BlueprintRecipe($ironTraverseBlueprint);
            $traversing->setMainProductCount(100);
            $traversing->setDescription("Traversing");
            $traversing->addTool($workerBlueprint, 10);
            $plating->addInputBlueprint($ironOreBlueprint, 120);
        }

        $wheet = new Concept\Food();
        $wheet->setEnergy(8000);
        $deposit->addResourceDescriptors($this->newThings(5000, $wb = $wheet->getBlueprint("Wheet")));
        {
            $smallFarming = new PlanetEntity\Resource\BlueprintRecipe($wb);
            $smallFarming->setDescription("Carefull farming");
            $smallFarming->setMainProductCount(20);
            $smallFarming->addInputBlueprint($wb, 1);
            $smallFarming->addTool($farmerBlueprint, 5);
            $smallFarming->addTool(Concept\Farm::class, 1);

            $bigFarming = new PlanetEntity\Resource\BlueprintRecipe($wb);
            $bigFarming->setDescription("Agro industry");
            $bigFarming->setMainProductCount(15000);
            $bigFarming->addInputBlueprint($wb, 1000);
            $bigFarming->addTool($farmerBlueprint, 500);
            $bigFarming->addTool(Concept\Farm::class, 1000);
        }

        $container = new Concept\Container();
        $container->setArea(60);
        $container->setWeight(1000);
        $deposit->addResourceDescriptors($this->newThings(5000, $containerBlueprint = $container->getBlueprint("Generic container")));
        {
            $creation = new PlanetEntity\Resource\BlueprintRecipe($containerBlueprint);
            $creation->setDescription("Container creation");
            $creation->addTool($teamWorkers, 10);
            $creation->addInputBlueprint($ironPlateBlueprint, 4);
        }

        $containerWarehouse = new Concept\Warehouse();
        $containerWarehouse->setSkeleton($container);
        $containerWarehouse->setArea(60);
        $containerWarehouse->setSpaceCapacity(350);
        $containerWarehouse->setWeightCapacity(10000);
        $deposit->addResourceDescriptors($this->newThings(3, $containerWarehouse = $containerWarehouse->getBlueprint("Container warehouse")));
        {
            $place = new PlanetEntity\Resource\BlueprintRecipe($containerWarehouse);
            $place->setDescription("Make warehouse from container");
            $place->addTool($builderTeamBlueprint, 10);
            $place->addInputBlueprint($containerBlueprint);
        }

        $containerHouse = new Concept\House();
        $containerHouse->setArea(60);
        $containerHouse->setPeopleCapacity(3);
        $containerHouse->setPeopleMaxCapacity(30);
        $deposit->addResourceDescriptors($this->newThings(10, $containerHouse = $containerHouse->getBlueprint("Container house")));
        {
            $place = new PlanetEntity\Resource\BlueprintRecipe($containerHouse);
            $place->setDescription("Make house from container");
            $place->addTool($builderTeamBlueprint, 10);
            $place->addInputBlueprint($containerBlueprint);
        }
    }

    private function newThings($count, PlanetEntity\Resource\Blueprint $blueprint) {
        $thing = new PlanetEntity\Resource\Thing();
        $thing->setBlueprint($blueprint);
        $thing->setAmount($count);
        return $thing;
    }

}