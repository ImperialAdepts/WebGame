<?php
namespace AppBundle\Fixture;

use AppBundle\Entity as GeneralEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use PlanetBundle\Entity as PlanetEntity;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * vygeneruje informace o testovaci planete
 */
class PlanetsFixture extends Fixture implements ContainerAwareInterface, FixtureInterface
{
    /**
     * The dependency injection container.
     *
     * @var ContainerInterface
     */
    protected $container;

	/**
	 * Load data fixtures with the passed EntityManager
	 *
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(\Doctrine\Common\Persistence\ObjectManager $manager)
	{
        echo __CLASS__."\n";
        $manager = $this->container->get('doctrine.orm.entity_manager');

	    $solarSystemModel = new GeneralEntity\SolarSystem\System();
	    $solarSystemModel->setName("The solar system");
	    $solarSystemModel->setSectorAddress(GeneralEntity\Galaxy\SectorAddress::createZeroSectorAddress());
	    $solarSystemModel->setLocalGroupCoordination(new GeneralEntity\Galaxy\SpaceCoordination(0,0,1));
        $manager->persist($solarSystemModel);

	    $sun = new GeneralEntity\SolarSystem\Planet();
	    $sun->setDiameter(2*696342);
	    $sun->setWeight(19885*(10^17));
	    $sun->setGravity(28);
	    $sun->setSystem($solarSystemModel);
        $manager->persist($sun);
        $solarSystemModel->setCentralSun($sun);

        $earth = new GeneralEntity\SolarSystem\Planet();
        $earth->setType('earth');
        $earth->setSystem($solarSystemModel);
        $earth->setGravity(1);
        $earth->setWeight(597237*10^8);
        $earth->setDiameter(2*6371);
        $earth->setOrbitDiameter(300);
        $earth->setOrbitingCenter($sun);
        $earth->setOrbitPeriod(360);
        $earth->setSurfaceGranularity(10);
        $earth->setDatabaseCredentials([
            'database_host' => $this->container->getParameter('planet2_database_host'),
            'database_port' => $this->container->getParameter('planet2_database_port'),
            'database_name' => $this->container->getParameter('planet2_database_name'),
            'database_user' => $this->container->getParameter('planet2_database_user'),
            'database_password' => $this->container->getParameter('planet2_database_password'),
        ]);
        $manager->persist($earth);

        $testSystem = new GeneralEntity\SolarSystem\System();
        $testSystem->setName("Mess");
        $testSystem->setSectorAddress(GeneralEntity\Galaxy\SectorAddress::createZeroSectorAddress());
        $testSystem->setLocalGroupCoordination(new GeneralEntity\Galaxy\SpaceCoordination(0,0,0));
        $manager->persist($testSystem);

        $testStar = new GeneralEntity\SolarSystem\Planet();
        $testStar->setDiameter(2*696342);
        $testStar->setWeight(19885*(10^17));
        $testStar->setGravity(28);
        $testStar->setSystem($testSystem);
        $manager->persist($testStar);
        $testSystem->setCentralSun($testStar);

        $testPlanet = new GeneralEntity\SolarSystem\Planet();
        $testPlanet->setType('test');
        $testPlanet->setSystem($testSystem);
        $testPlanet->setGravity(1);
        $testPlanet->setWeight(597237*10^8);
        $testPlanet->setDiameter(30);
        $testPlanet->setOrbitDiameter(150);
        $testPlanet->setOrbitingCenter($testStar);
        $testPlanet->setOrbitPeriod(60);
        $testPlanet->setSurfaceGranularity(5);
        $testPlanet->setDatabaseCredentials([
            'database_host' => $this->container->getParameter('planet1_database_host'),
            'database_port' => $this->container->getParameter('planet1_database_port'),
            'database_name' => $this->container->getParameter('planet1_database_name'),
            'database_user' => $this->container->getParameter('planet1_database_user'),
            'database_password' => $this->container->getParameter('planet1_database_password'),
        ]);
        $manager->persist($testPlanet);

        $event = new GeneralEntity\Human\Event();
        $event->setDescription(GeneralEntity\Human\EventTypeEnum::PLANET_MAP_GENERATION);
        $event->setPlanet($testPlanet);
        $event->setPlanetPhase(0);
        $event->setTime(time());

        $manager->persist($event);

		$manager->flush();
	}

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

}