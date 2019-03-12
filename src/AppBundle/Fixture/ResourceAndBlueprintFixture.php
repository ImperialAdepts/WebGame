<?php
namespace AppBundle\Fixture;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Entity;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ResourceAndBlueprintFixture extends \Doctrine\Bundle\FixturesBundle\Fixture implements ContainerAwareInterface, DependentFixtureInterface
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
		foreach ($this->container->getParameter('default_blueprints') as $name => $blueprintData) {
            $blueprint = $this->createBlueprint(
                $name,
                $name,
                $blueprintData['building_requirements'],
                $blueprintData['constraints'],
                $blueprintData['useCases'],
                $blueprintData['trait_values']
            );
            $manager->persist($blueprint);
        }
        $manager->flush();

		$builder = new \AppBundle\Builder\PlanetBuilder($manager, $this->container->getParameter('default_colonization_packs'));
		$humans = $manager->getRepository(Entity\Human::class)->findAllIncarnated();
		$regions = $manager->getRepository(Entity\Planet\Region::class)->findAll();
		$regionCounter = 1;
		/** @var Entity\Human $human */
        foreach ($humans as $human) {
            /** @var Entity\Planet\Region $centralRegion */
            $centralRegion = $regions[$regionCounter];
            $regionCounter += 4;
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

	private function createBlueprint($name, $resource, array $requirements = [], array $constraints = [], array $useCases = [], array $traitValues = [])
	{
		$blueprint = new Entity\Blueprint();
		$blueprint->setDescription($name);
		$blueprint->setResourceDescriptor($resource);
		$blueprint->setRequirements($requirements);
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
        ];
    }
}