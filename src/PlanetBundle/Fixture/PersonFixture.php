<?php
namespace PlanetBundle\Fixture;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use PlanetBundle\Entity as PlanetEntity;
use AppBundle\Entity as GlobalEntity;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PersonFixture extends Fixture implements ContainerAwareInterface
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
	 * @param ObjectManager $manager
	 */
	public function load(ObjectManager $manager)
	{
        $generalManager = $this->container->get('doctrine.orm.entity_manager');
		$troi = new GlobalEntity\Gamer();
		$troi->setLogin('troi');
		$troi->setPassword('troi');
        $generalManager->persist($troi);

        $soul = new GlobalEntity\Soul();
        $soul->setGamer($troi);
        $soul->setName('Odin');
        $generalManager->persist($soul);

        $globalHuman1 = new GlobalEntity\Human();
        $globalHuman1->setName('Erik krvava sekera');
        $globalHuman1->setSoul($soul);
        $generalManager->persist($globalHuman1);

        $globalHuman2 = new GlobalEntity\Human();
        $globalHuman2->setName('Rudovous');
        $globalHuman2->setSoul($soul);
        $generalManager->persist($globalHuman2);

        $soul = new GlobalEntity\Soul();
        $soul->setGamer($troi);
        $soul->setName('Zeus');
        $generalManager->persist($soul);

        $globalHuman3 = new GlobalEntity\Human();
        $globalHuman3->setName('Herakles');
        $globalHuman3->setSoul($soul);
        $generalManager->persist($globalHuman3);

        $globalHuman4 = new GlobalEntity\Human();
        $globalHuman4->setName('Oidipus');
        $globalHuman4->setSoul($soul);
        $generalManager->persist($globalHuman4);

        $generalManager->flush();


        $human = new PlanetEntity\Human();
        $human->setName('Erik krvava sekera');
        $human->setBornIn(0);
        $human->setGlobalHumanId($globalHuman1->getId());
        $manager->persist($human);

        $human = new PlanetEntity\Human();
        $human->setName('Rudovous');
        $human->setBornIn(0);
        $human->setGlobalHumanId($globalHuman2->getId());
        $manager->persist($human);

        $human = new PlanetEntity\Human();
        $human->setName('Herakles');
        $human->setBornIn(0);
        $human->setGlobalHumanId($globalHuman3->getId());
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