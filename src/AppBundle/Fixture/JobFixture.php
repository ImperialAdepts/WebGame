<?php
namespace AppBundle\Fixture;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Entity; use PlanetBundle\Entity as PlanetEntity;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tracy\Debugger;

class JobFixture extends \Doctrine\Bundle\FixturesBundle\Fixture implements ContainerAwareInterface, DependentFixtureInterface
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
        $farmingBlueprints = $manager->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::TYPE_FARMING);
        $productionBlueprints = $manager->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::TYPE_PRODUCTION);

		$regions = $manager->getRepository(PlanetEntity\Region::class)->findAll();
        foreach ($regions as $region) {
            if ($region->getSettlement() != null) {
                foreach ($farmingBlueprints as $blueprint) {
                    $farmingJob = new Entity\Job\ProduceJob();
                    $farmingJob->setRegion($region);
                    $farmingJob->setAmount(4);
                    $farmingJob->setRepetition(null);
                    $farmingJob->setBlueprint($blueprint);
                    $manager->persist($farmingJob);
                }
                foreach ($productionBlueprints as $blueprint) {
                    $productionJob = new Entity\Job\ProduceJob();
                    $productionJob->setRegion($region);
                    $productionJob->setAmount(4);
                    $productionJob->setRepetition(null);
                    $productionJob->setBlueprint($blueprint);
                    $manager->persist($productionJob);
                }
            }
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


    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            ResourceAndBlueprintFixture::class,
            TeamFixture::class,
        ];
    }
}