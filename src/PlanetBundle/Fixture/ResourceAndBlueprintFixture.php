<?php
namespace PlanetBundle\Fixture;

use AppBundle\Entity as GeneralEntity;
use PlanetBundle\Entity as PlanetEntity;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tracy\Debugger;

class ResourceAndBlueprintFixture extends \Doctrine\Bundle\FixturesBundle\Fixture implements ContainerAwareInterface, DependentFixtureInterface
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
        $testPlanet = $generalManager->getRepository(GeneralEntity\SolarSystem\Planet::class)->findOneBy(['type'=>'test']);
        $this->container->get('dynamic_planet_connector')->setPlanet($testPlanet);
        $manager = $this->container->get('doctrine')->getManager('planet');

		foreach ($this->container->getParameter('default_blueprints') as $name => $blueprintData) {
            $blueprint = $this->createBlueprint(
                $name,
                $name,
                $blueprintData['building_resource_requirements'],
                $blueprintData['building_usecase_requirements'],
                $blueprintData['constraints'],
                $blueprintData['useCases'],
                $blueprintData['trait_values']
            );
            $manager->persist($blueprint);
        }
        $manager->flush();

		$builder = new \AppBundle\Builder\PlanetBuilder($manager, $this->container->getParameter('default_colonization_packs'));
		$humans = $manager->getRepository(PlanetEntity\Human::class)->findAllIncarnated();
		$regions = $manager->getRepository(PlanetEntity\Region::class)->findAll();

		$regionCounter = 1;
		/** @var PlanetEntity\Human $human */
        foreach ($humans as $human) {
            /** @var PlanetEntity\Region $centralRegion */
            $centralRegion = $regions[$regionCounter];
            $regionCounter += 100;
			$builder->newColony($centralRegion, $human, 'simple');
			$human->setCurrentPosition($centralRegion->getSettlement());
		}

		$manager->flush();
	}

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

	private function createBlueprint($name, $resource, array $resourceRequirements = [], array $useCaseRequirements = [], array $constraints = [], array $useCases = [], array $traitValues = [])
	{
		$blueprint = new PlanetEntity\Blueprint();
		$blueprint->setDescription($name);
		$blueprint->setResourceDescriptor($resource);
		$blueprint->setResourceRequirements($resourceRequirements);
		$blueprint->setUseCaseRequirements($useCaseRequirements);
		$blueprint->setConstraints($constraints);
		$blueprint->setUseCases($useCases);
		$blueprint->setTraitValues($traitValues);
		return $blueprint;
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
            PersonFixture::class,
            PlanetMapFixture::class,
        ];
    }
}