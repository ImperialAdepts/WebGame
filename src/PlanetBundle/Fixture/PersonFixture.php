<?php
namespace PlanetBundle\Fixture;

use AppBundle\EnumAlignmentType;
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
        $testPlanet = $generalManager->getRepository(GlobalEntity\SolarSystem\Planet::class, 'default')->findOneBy(['type'=>'test']);

		$troi = new GlobalEntity\Gamer();
		$troi->setLogin('troi');
		$troi->setPassword('troi');
        $generalManager->persist($troi);

        $soul = new GlobalEntity\Soul();
        $soul->setGamer($troi);
        $soul->setName('Odin');
        $soul->setAlignment(EnumAlignmentType::LAWFUL_EVIL);
        $soul->addPreference(GlobalEntity\rpg\SoulPreference::create(GlobalEntity\rpg\SoulPreferenceTypeEnum::EVIL_BEST, $soul));
        $soul->addPreference(GlobalEntity\rpg\SoulPreference::create(GlobalEntity\rpg\SoulPreferenceTypeEnum::EVERYBODY_ONLY_FRIENDS, $soul));
        $soul->addPreference(GlobalEntity\rpg\SoulPreference::create(GlobalEntity\rpg\SoulPreferenceTypeEnum::EVIL_LIFE_NOT_MATTER, $soul));
        $soul->addPreference(GlobalEntity\rpg\SoulPreference::create(GlobalEntity\rpg\SoulPreferenceTypeEnum::EVIL_ODINIST, $soul));
        $generalManager->persist($soul);

        $globalHuman1 = new GlobalEntity\Human();
        $globalHuman1->setName('Erik krvava sekera');
        $globalHuman1->setSoul($soul);
        $globalHuman1->setPlanet($testPlanet);
        $globalHuman1->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::FAMILY_OPINION, 'STRONG', $globalHuman1));
        $globalHuman1->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::KNOWLEDGE_VALUE, 'NONE', $globalHuman1));
        $globalHuman1->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::FORTUNE_SENSE, 'WEAK', $globalHuman1));
        $generalManager->persist($globalHuman1);

        $globalHuman2 = new GlobalEntity\Human();
        $globalHuman2->setName('Rudovous');
        $globalHuman2->setSoul($soul);
        $globalHuman2->setPlanet($testPlanet);
        $generalManager->persist($globalHuman2);

        $soul = new GlobalEntity\Soul();
        $soul->setGamer($troi);
        $soul->setName('Zeus');
        $soul->setAlignment(EnumAlignmentType::LAWFUL_NEUTRAL);
        $soul->addPreference(GlobalEntity\rpg\SoulPreference::create(GlobalEntity\rpg\SoulPreferenceTypeEnum::EVIL_BEST, $soul));
        $soul->addPreference(GlobalEntity\rpg\SoulPreference::create(GlobalEntity\rpg\SoulPreferenceTypeEnum::EVERYBODY_ONLY_FRIENDS, $soul));
        $soul->addPreference(GlobalEntity\rpg\SoulPreference::create(GlobalEntity\rpg\SoulPreferenceTypeEnum::MORAL_NEUTRAL_SIZE_EQUILIBRIUM, $soul));
        $soul->addPreference(GlobalEntity\rpg\SoulPreference::create(GlobalEntity\rpg\SoulPreferenceTypeEnum::MORAL_NEUTRAL_POWER_EQUILIBRIUM, $soul));
        $generalManager->persist($soul);

        $globalHuman3 = new GlobalEntity\Human();
        $globalHuman3->setName('Herakles');
        $globalHuman3->setSoul($soul);
        $globalHuman3->setPlanet($testPlanet);
        $globalHuman3->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::FAMILY_OPINION, 'WEAK', $globalHuman3));
        $globalHuman3->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::KNOWLEDGE_VALUE, 'WEAK', $globalHuman3));
        $globalHuman3->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::FORTUNE_SENSE, 'STRONG', $globalHuman3));
        $generalManager->persist($globalHuman3);

        $globalHuman4 = new GlobalEntity\Human();
        $globalHuman4->setName('Oidipus');
        $globalHuman4->setSoul($soul);
        $globalHuman4->setPlanet($testPlanet);
        $globalHuman4->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::FAMILY_OWN_CHILDREN, 'STRONG', $globalHuman4));
        $globalHuman4->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::KNOWLEDGE_VALUE, 'STRONG', $globalHuman4));
        $globalHuman4->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::VIOLENCE, 'STRONG', $globalHuman4));
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