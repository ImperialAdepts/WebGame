<?php
namespace PlanetBundle\Fixture;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Entity as GeneralEntity;
use AppBundle\Fixture\PlanetsFixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use PlanetBundle\Builder\RegionTerrainTypeEnumBuilder;
use PlanetBundle\Concept;
use PlanetBundle\Entity as PlanetEntity;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tracy\Debugger;

class StandardColonizationShipFixture extends \Doctrine\Bundle\FixturesBundle\Fixture implements ContainerAwareInterface//, DependentFixtureInterface
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
            $this->setReference(self::DEPOSIT_CODE, $deposit);
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

    private function fillDeposit(PlanetEntity\Resource\StandardizedDeposit $deposit)
    {
        $human = new Concept\People();
        $deposit->addResourceDescriptors($this->newThings(100, $human->getBlueprint("Earthling")));

        $wheet = new Concept\Food();
        $wheet->setEnergy(8000);
        $deposit->addResourceDescriptors($this->newThings(5000, $wheet->getBlueprint("Wheet")));

        $containerWarehouse = new Concept\Warehouse();
        $containerWarehouse->setArea(60);
        $containerWarehouse->setSpaceCapacity(350);
        $containerWarehouse->setWeightCapacity(10000);
        $deposit->addResourceDescriptors($this->newThings(3, $containerWarehouse->getBlueprint("Container warehouse")));

        $containerHouse = new Concept\LivingBuilding();
        $containerHouse->setArea(60);
        $containerHouse->setPeopleCapacity(3);
        $containerHouse->setPeopleMaxCapacity(30);
        $deposit->addResourceDescriptors($this->newThings(10, $containerWarehouse->getBlueprint("Container house")));

        return;

    }

    private function newThings($count, PlanetEntity\Resource\Blueprint $blueprint) {
        $thing = new PlanetEntity\Resource\Thing();
        $thing->setBlueprint($blueprint);
        $thing->setAmount($count);
        return $thing;
    }

    /**
     * This method must return an array of groups
     * on which the implementing class belongs to
     *
     * @return string[]
     */
    public static function getGroups(): array
    {
        return ['test'];
    }
}