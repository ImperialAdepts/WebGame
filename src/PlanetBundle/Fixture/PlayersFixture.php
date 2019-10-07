<?php
namespace PlanetBundle\Fixture;

use AppBundle\Builder\PlanetBuilder;
use AppBundle\Entity as GeneralEntity;
use AppBundle\Fixture\PlanetsFixture;
use PlanetBundle\Entity as PlanetEntity;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PlayersFixture extends \Doctrine\Bundle\FixturesBundle\Fixture implements ContainerAwareInterface, DependentFixtureInterface
{
    /**
     * The dependency injection container.
     *
     * @var ContainerInterface
     */
    protected $container;

	public function load(\Doctrine\Common\Persistence\ObjectManager $generalManager)
	{
        echo __CLASS__."\n";
        $planets = $generalManager->getRepository(GeneralEntity\SolarSystem\Planet::class)->findAll();
        foreach ($planets as $planet) {
            if ($planet->getDatabaseCredentials() == null) continue;

            $this->container->get('dynamic_planet_connector')->setPlanet($planet, true);
            $manager = $this->container->get('doctrine')->getManager('planet');

            /** @var PlanetBuilder $builder */
            $builder = new \AppBundle\Builder\PlanetBuilder($generalManager, $manager, $this->container->getParameter('default_colonization_packs'));
            $humans = $manager->getRepository(PlanetEntity\Human::class)->findAll();
            $peaks = $manager->getRepository(PlanetEntity\Peak::class)->findAll();

            $peakCounter = floor(count($peaks)/3);
            /** @var PlanetEntity\Human $human */
            foreach ($humans as $human) {
                /** @var PlanetEntity\Peak $administrativeCenter */
                $administrativeCenter = $peaks[$peakCounter];
                $peakCounter += ceil(count($peaks) / count($humans));
                $peakCounter = $peakCounter % count($peaks);
                $builder->newColony($administrativeCenter, $human, 'simple');
                $human->setCurrentPeakPosition($administrativeCenter);

                $globalHuman = $generalManager->getRepository(GeneralEntity\Human::class)->find($human->getGlobalHumanId());
                echo "Planet {$planet->getName()} settlement generated for {$globalHuman->getName()}\n";
            }

            $manager->flush();
            $generalManager->flush();
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
            TroiColoniesFixture::class,
        ];
    }
}