<?php
namespace AppBundle\Fixture;

use PlanetBundle\Entity\Human;

class PersonFixture extends \Doctrine\Bundle\FixturesBundle\Fixture
{

	/**
	 * Load data fixtures with the passed EntityManager
	 *
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(\Doctrine\Common\Persistence\ObjectManager $manager)
	{
		$troi = new \AppBundle\Entity\Gamer();
		$troi->setLogin('troi');
		$troi->setPassword('troi');
		$manager->persist($troi);

        {
            $soul = new \AppBundle\Entity\Soul();
            $soul->setGamer($troi);
            $soul->setName('Odin');
            $manager->persist($soul);
            {
                $human = new Human();
                $human->setName('Erik krvava sekera');
                $human->setBornIn(0);
                $human->setSoul($soul);
                $manager->persist($human);
            }
            {
                $human = new Human();
                $human->setName('Rudovous');
                $human->setBornIn(0);
                $human->setSoul($soul);
                $manager->persist($human);
            }
        }
        {
            $soul = new \AppBundle\Entity\Soul();
            $soul->setGamer($troi);
            $soul->setName('Zeus');
            $manager->persist($soul);
            {
                $human = new Human();
                $human->setName('Herakles');
                $human->setBornIn(0);
                $human->setSoul($soul);
                $manager->persist($human);
            }
            {
                $human = new Human();
                $human->setName('Oidipus');
                $human->setBornIn(0);
                $human->setSoul($soul);
                $manager->persist($human);
            }
        }

		$manager->flush();

		echo "DONE";
	}
}