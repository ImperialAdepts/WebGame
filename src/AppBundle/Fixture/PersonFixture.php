<?php
namespace AppBundle\Fixture;

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

		$king1 = new \AppBundle\Entity\Soul();
		$king1->setGamer($troi);
		$king1->setName('King I.');
		$manager->persist($king1);

		$human1 = new \AppBundle\Entity\Human();
		$human1->setName('Odin');
		$human1->setBornIn(0);
		$human1->setSoul($king1);
		$manager->persist($human1);

		$human2 = new \AppBundle\Entity\Human();
		$human2->setName('Zeus');
		$human2->setBornIn(10);
		$human2->setSoul($king1);
		$manager->persist($human2);

		$human3 = new \AppBundle\Entity\Human();
		$human3->setName('Persefone');
		$human3->setBornIn(20);
		$human3->setSoul($king1);
		$manager->persist($human3);

		$manager->flush();

		echo "DONE";
	}
}