<?php
namespace PlanetBundle\Fixture;

use AppBundle\Descriptor\TimeTransformator;
use AppBundle\EnumAlignmentType;
use AppBundle\Fixture\PlanetsFixture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use PlanetBundle\Builder\PlanetMapBuilder;
use PlanetBundle\Entity as PlanetEntity;
use AppBundle\Entity as GeneralEntity;
use PlanetBundle\Maintainer\PlanetMaintainer;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FirstPlanetPhaseFixture extends Fixture implements ContainerAwareInterface, DependentFixtureInterface
{
    /**
     * The dependency injection container.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ObjectManager $generalManager
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
	public function load(ObjectManager $generalManager)
	{
        echo __CLASS__."\n";

        /** @var GeneralEntity\SolarSystem\Planet $planets */
        $planets = $generalManager->getRepository(GeneralEntity\SolarSystem\Planet::class)->findAll();
        /** @var GeneralEntity\SolarSystem\Planet $planet */
        foreach ($planets as $planet) {
            if ($planet->getDatabaseCredentials() == null) continue;

            $this->container->get('dynamic_planet_connector')->setPlanet($planet, true);

            $planetMaintainer = $this->container->get('maintainer_planet');
            $planetMaintainer->goToNewPlanetPhase();

            $generalManager->flush();
            $this->container->get('doctrine.orm.planet_entity_manager')->flush();
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
            PersonFixture::class,
            PlanetMapFixture::class,
            ResourceAndBlueprintFixture::class,
            TeamFixture::class,
            JobFixture::class,
        ];
    }
}