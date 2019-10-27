<?php
namespace PlanetBundle\Fixture;

use AppBundle\Descriptor\TimeTransformator;
use AppBundle\EnumAlignmentType;
use AppBundle\Fixture\PlanetMapFixture;
use AppBundle\Fixture\PlanetsFixture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use PlanetBundle\Entity as PlanetEntity;
use AppBundle\Entity as GlobalEntity;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PersonFixture extends Fixture implements ContainerAwareInterface, DependentFixtureInterface
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
        $test1Planet = $generalManager->getRepository(GlobalEntity\SolarSystem\Planet::class)->findOneBy(['type'=>'test']);
        $test2Planet = $generalManager->getRepository(GlobalEntity\SolarSystem\Planet::class)->findOneBy(['type'=>'EARTH']);

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
        $globalHuman1->setPlanet($test1Planet);
        $globalHuman1->setBornPlanet($test1Planet);
        $globalHuman1->setBornPhase(TimeTransformator::timestampToPhase($test1Planet, time()));
        $globalHuman1->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::FAMILY_OPINION, 'STRONG', $globalHuman1));
        $globalHuman1->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::KNOWLEDGE_VALUE, 'NONE', $globalHuman1));
        $globalHuman1->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::FORTUNE_SENSE, 'WEAK', $globalHuman1));
        $globalHuman1->addKnowledge(GlobalEntity\rpg\Knowledge::create(GlobalEntity\rpg\KnowledgeTypeEnum::BUILDING_SIMPLE, 80, $globalHuman1));
        $globalHuman1->addKnowledge(GlobalEntity\rpg\Knowledge::create(GlobalEntity\rpg\KnowledgeTypeEnum::MANAGEMENT_SETTLEMENT, 10, $globalHuman1));
        $globalHuman1->addKnowledge(GlobalEntity\rpg\Knowledge::create(GlobalEntity\rpg\KnowledgeTypeEnum::BUILDING_INFRASTRUCTURE, 300, $globalHuman1));
        $generalManager->persist($globalHuman1);

        $globalHuman2 = new GlobalEntity\Human();
        $globalHuman2->setName('Rudovous');
        $globalHuman2->setSoul($soul);
        $globalHuman2->setPlanet($test2Planet);
        $globalHuman2->setBornPlanet($test2Planet);
        $globalHuman2->setBornPhase(TimeTransformator::timestampToPhase($test2Planet, time()));
        $globalHuman2->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::FAMILY_OPINION, 'STRONG', $globalHuman2));
        $globalHuman2->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::KNOWLEDGE_VALUE, 'NONE', $globalHuman2));
        $globalHuman2->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::FORTUNE_SENSE, 'WEAK', $globalHuman2));
        $globalHuman2->addKnowledge(GlobalEntity\rpg\Knowledge::create(GlobalEntity\rpg\KnowledgeTypeEnum::BUILDING_SIMPLE, 80, $globalHuman2));
        $globalHuman2->addKnowledge(GlobalEntity\rpg\Knowledge::create(GlobalEntity\rpg\KnowledgeTypeEnum::MANAGEMENT_SETTLEMENT, 10, $globalHuman2));
        $globalHuman2->addKnowledge(GlobalEntity\rpg\Knowledge::create(GlobalEntity\rpg\KnowledgeTypeEnum::BUILDING_INFRASTRUCTURE, 300, $globalHuman2));
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
        $globalHuman3->setPlanet($test1Planet);
        $globalHuman3->setBornPlanet($test1Planet);
        $globalHuman3->setBornPhase(TimeTransformator::timestampToPhase($test1Planet, time()));
        $globalHuman3->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::FAMILY_OPINION, 'WEAK', $globalHuman3));
        $globalHuman3->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::KNOWLEDGE_VALUE, 'WEAK', $globalHuman3));
        $globalHuman3->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::FORTUNE_SENSE, 'STRONG', $globalHuman3));
        $globalHuman3->addKnowledge(GlobalEntity\rpg\Knowledge::create(GlobalEntity\rpg\KnowledgeTypeEnum::BUILDING_INFRASTRUCTURE, 10, $globalHuman3));
        $globalHuman3->addKnowledge(GlobalEntity\rpg\Knowledge::create(GlobalEntity\rpg\KnowledgeTypeEnum::BUILDING_SIMPLE, 150, $globalHuman3));
        $generalManager->persist($globalHuman3);

        $globalHuman4 = new GlobalEntity\Human();
        $globalHuman4->setName('Oidipus');
        $globalHuman4->setSoul($soul);
        $globalHuman4->setPlanet($test2Planet);
        $globalHuman4->setBornPlanet($test2Planet);
        $globalHuman4->setBornPhase(TimeTransformator::timestampToPhase($test2Planet, time()));
        $globalHuman4->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::FAMILY_OWN_CHILDREN, 'STRONG', $globalHuman4));
        $globalHuman4->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::KNOWLEDGE_VALUE, 'STRONG', $globalHuman4));
        $globalHuman4->addPreference(GlobalEntity\rpg\HumanPreference::create(GlobalEntity\rpg\HumanPreferenceTypeEnum::VIOLENCE, 'STRONG', $globalHuman4));
        $globalHuman4->addKnowledge(GlobalEntity\rpg\Knowledge::create(GlobalEntity\rpg\KnowledgeTypeEnum::BUILDING_INFRASTRUCTURE, 20, $globalHuman4));
        $globalHuman4->addKnowledge(GlobalEntity\rpg\Knowledge::create(GlobalEntity\rpg\KnowledgeTypeEnum::BUILDING_TRANSPORT_VEHICLES, 90, $globalHuman4));
        $generalManager->persist($globalHuman4);

        $generalManager->flush();

        $this->container->get('dynamic_planet_connector')->setPlanet($test1Planet, true);
        $manager = $this->container->get('doctrine')->getManager('planet');

        $human = new PlanetEntity\Human();
        $human->setGlobalHumanId($globalHuman1->getId());
        $manager->persist($human);

        $human = new PlanetEntity\Human();
        $human->setGlobalHumanId($globalHuman3->getId());
        $manager->persist($human);

        $manager->flush();

        $this->container->get('dynamic_planet_connector')->setPlanet($test2Planet, true);
        $manager = $this->container->get('doctrine')->getManager('planet');

        $human = new PlanetEntity\Human();
        $human->setGlobalHumanId($globalHuman2->getId());
        $manager->persist($human);

        $human = new PlanetEntity\Human();
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
            PlanetMapFixture::class,
        ];
    }

}