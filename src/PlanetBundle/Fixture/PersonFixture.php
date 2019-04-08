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

        {
            $soul = new GlobalEntity\Soul();
            $soul->setGamer($troi);
            $soul->setName('Odin');
            $generalManager->persist($soul);
            {
                $globalHuman = new GlobalEntity\Human();
                $globalHuman->setName('Erik krvava sekera');
                $globalHuman->setSoul($soul);
                $generalManager->persist($globalHuman);

                $human = new PlanetEntity\Human();
                $human->setName('Erik krvava sekera');
                $human->setBornIn(0);
                $human->setGlobalHumanId($globalHuman->getId());
                $manager->persist($human);
            }
            {
                $globalHuman = new GlobalEntity\Human();
                $globalHuman->setName('Rudovous');
                $globalHuman->setSoul($soul);
                $generalManager->persist($globalHuman);

                $human = new PlanetEntity\Human();
                $human->setName('Rudovous');
                $human->setBornIn(0);
                $human->setGlobalHumanId($globalHuman->getId());
                $manager->persist($human);
            }
        }
        {
            $soul = new GlobalEntity\Soul();
            $soul->setGamer($troi);
            $soul->setName('Zeus');
            $generalManager->persist($soul);
            {
                $globalHuman = new GlobalEntity\Human();
                $globalHuman->setName('Herakles');
                $globalHuman->setSoul($soul);
                $generalManager->persist($globalHuman);

                $human = new PlanetEntity\Human();
                $human->setName('Herakles');
                $human->setBornIn(0);
                $human->setGlobalHumanId($globalHuman->getId());
                $manager->persist($human);
            }
            {
                $globalHuman = new GlobalEntity\Human();
                $globalHuman->setName('Oidipus');
                $globalHuman->setSoul($soul);
                $generalManager->persist($globalHuman);

                $human = new PlanetEntity\Human();
                $human->setName('Oidipus');
                $human->setBornIn(0);
                $human->setGlobalHumanId($globalHuman->getId());
                $manager->persist($human);
            }
        }

		$manager->flush();

		echo "DONE";
	}

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}