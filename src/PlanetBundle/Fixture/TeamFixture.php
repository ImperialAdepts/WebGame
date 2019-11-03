<?php
namespace PlanetBundle\Fixture;

use AppBundle\Entity as GeneralEntity;
use AppBundle\Fixture\PlanetMapFixture;
use AppBundle\Fixture\PlanetsFixture;
use Doctrine\Common\Persistence\ObjectManager;
use PlanetBundle\Entity as PlanetEntity;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use PlanetBundle\UseCase\TeamBuilders;
use PlanetBundle\UseCase\TeamFarmers;
use PlanetBundle\UseCase\TeamMerchants;
use PlanetBundle\UseCase\TeamScientists;
use PlanetBundle\UseCase\TeamTransporters;
use PlanetBundle\UseCase\TeamWorkers;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TeamFixture extends \Doctrine\Bundle\FixturesBundle\Fixture implements ContainerAwareInterface, DependentFixtureInterface
{
    /**
     * The dependency injection container.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ObjectManager $generalManager
     * @throws \Exception
     */
	public function load(ObjectManager $generalManager)
	{
        echo __CLASS__."\n";
        $testPlanet = $generalManager->getRepository(GeneralEntity\SolarSystem\Planet::class)->findOneBy(['type'=>'test']);
        $this->container->get('dynamic_planet_connector')->setPlanet($testPlanet, true);

        /** @var ObjectManager $manager */
        $manager = $this->container->get('doctrine')->getManager('planet');
        $blueprints = array_merge(
            $transporterBlueprints = $manager->getRepository(PlanetEntity\Resource\Blueprint::class)->getByUseCase(TeamTransporters::class),
            $builderBlueprints = $manager->getRepository(PlanetEntity\Resource\Blueprint::class)->getByUseCase(TeamBuilders::class),
            $merchantBlueprints = $manager->getRepository(PlanetEntity\Resource\Blueprint::class)->getByUseCase(TeamMerchants::class),
            $scientistBlueprints = $manager->getRepository(PlanetEntity\Resource\Blueprint::class)->getByUseCase(TeamScientists::class),
            $workerBlueprints = $manager->getRepository(PlanetEntity\Resource\Blueprint::class)->getByUseCase(TeamWorkers::class),
            $farmerBlueprints = $manager->getRepository(PlanetEntity\Resource\Blueprint::class)->getByUseCase(TeamFarmers::class)
        );
		$regions = $manager->getRepository(PlanetEntity\Region::class)->findAll();
        foreach ($regions as $region) {
            if ($region->getSettlement() != null) {
                foreach ($blueprints as $blueprint) {
                    $region->addResourceDeposit($blueprint, 1);
                }
            }
		}

		$manager->flush();
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
            PlanetMapFixture::class,
            PlanetsFixture::class,
            PlayersFixture::class,
        ];
    }
}