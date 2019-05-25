<?php
namespace PlanetBundle\Fixture;

use AppBundle\Entity as GeneralEntity;
use AppBundle\Fixture\PlanetsFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use PlanetBundle\Builder\PlanetMapBuilder;
use PlanetBundle\Entity as PlanetEntity;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * vygeneruje malou testovaci mapu
 */
class PlanetMapFixture extends \Doctrine\Bundle\FixturesBundle\Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
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
            if ($planet->getDatabaseCredentials() == null) continue;

            $this->container->get('dynamic_planet_connector')->setPlanet($planet, true);

            $generator = new PlanetMapBuilder($this->container, $planet->getSurfaceGranularity());
            $generator->build($this->container->get('doctrine')->getManager('planet'), $planet);
            echo "planet done\n";
        }
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
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}