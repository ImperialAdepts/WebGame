<?php
namespace PlanetBundle\Fixture;

use AppBundle\EnumAlignmentType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use PlanetBundle\Entity as PlanetEntity;
use AppBundle\Entity as GlobalEntity;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tracy\Debugger;

class PersonFixture extends Fixture implements ContainerAwareInterface
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
        $test1Planet = $generalManager->getRepository(GlobalEntity\SolarSystem\Planet::class, 'default')->findOneBy(['type'=>'test']);
        $test2Planet = $generalManager->getRepository(GlobalEntity\SolarSystem\Planet::class, 'default')->findOneBy(['type'=>'EARTH']);

		$troi = new GlobalEntity\Gamer();
		$troi->setLogin('troi');
		$troi->setPassword('troi');
        $generalManager->persist($troi);

        $soul = new GlobalEntity\Soul();
        $soul->setGamer($troi);
        $soul->setName('Odin');
        $soul->setAlignment(EnumAlignmentType::LAWFUL_EVIL);
        $generalManager->persist($soul);

        $globalHuman1 = new GlobalEntity\Human();
        $globalHuman1->setName('Erik krvava sekera');
        $globalHuman1->setSoul($soul);
        $globalHuman1->setPlanet($test1Planet);
        $generalManager->persist($globalHuman1);

        $globalHuman2 = new GlobalEntity\Human();
        $globalHuman2->setName('Rudovous');
        $globalHuman2->setSoul($soul);
        $globalHuman2->setPlanet($test2Planet);
        $generalManager->persist($globalHuman2);

        $soul = new GlobalEntity\Soul();
        $soul->setGamer($troi);
        $soul->setName('Zeus');
        $soul->setAlignment(EnumAlignmentType::LAWFUL_NEUTRAL);
        $generalManager->persist($soul);

        $globalHuman3 = new GlobalEntity\Human();
        $globalHuman3->setName('Herakles');
        $globalHuman3->setSoul($soul);
        $globalHuman3->setPlanet($test1Planet);
        $generalManager->persist($globalHuman3);

        $globalHuman4 = new GlobalEntity\Human();
        $globalHuman4->setName('Oidipus');
        $globalHuman4->setSoul($soul);
        $globalHuman4->setPlanet($test2Planet);
        $generalManager->persist($globalHuman4);

        $generalManager->flush();

        $this->container->get('dynamic_planet_connector')->setPlanet($test1Planet, true);
        $manager = $this->container->get('doctrine')->getManager('planet');

        $human = new PlanetEntity\Human();
        $human->setName('Erik krvava sekera');
        $human->setBornIn(0);
        $human->setGlobalHumanId($globalHuman1->getId());
        $manager->persist($human);

        $human = new PlanetEntity\Human();
        $human->setName('Herakles');
        $human->setBornIn(0);
        $human->setGlobalHumanId($globalHuman3->getId());
        $manager->persist($human);

        $manager->flush();

        $this->container->get('dynamic_planet_connector')->setPlanet($test2Planet, true);
        $manager = $this->container->get('doctrine')->getManager('planet');

        $human = new PlanetEntity\Human();
        $human->setName('Rudovous');
        $human->setBornIn(0);
        $human->setGlobalHumanId($globalHuman2->getId());
        $manager->persist($human);

        $human = new PlanetEntity\Human();
        $human->setName('Oidipus');
        $human->setBornIn(0);
        $human->setGlobalHumanId($globalHuman4->getId());
        $manager->persist($human);

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