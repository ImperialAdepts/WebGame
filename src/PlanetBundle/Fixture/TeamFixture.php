<?php
namespace PlanetBundle\Fixture;

use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Entity as GeneralEntity;
use PlanetBundle\Entity as PlanetEntity;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TeamFixture extends \Doctrine\Bundle\FixturesBundle\Fixture implements ContainerAwareInterface, DependentFixtureInterface
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
        $blueprints = array_merge(
            $transporterBlueprints = $manager->getRepository(PlanetEntity\Blueprint::class)->getByUseCase(UseCaseEnum::TEAM_TRANSPORTERS),
            $builderBlueprints = $manager->getRepository(PlanetEntity\Blueprint::class)->getByUseCase(UseCaseEnum::TEAM_BUILDERS),
            $merchantBlueprints = $manager->getRepository(PlanetEntity\Blueprint::class)->getByUseCase(UseCaseEnum::TEAM_MERCHANTS),
            $scientistBlueprints = $manager->getRepository(PlanetEntity\Blueprint::class)->getByUseCase(UseCaseEnum::TEAM_SCIENTISTS),
            $workerBlueprints = $manager->getRepository(PlanetEntity\Blueprint::class)->getByUseCase(UseCaseEnum::TEAM_WORKERS),
            $farmerBlueprints = $manager->getRepository(PlanetEntity\Blueprint::class)->getByUseCase(UseCaseEnum::TEAM_FARMERS)
        );
		$regions = $manager->getRepository(PlanetEntity\Region::class)->findAll();
        foreach ($regions as $region) {
            if ($region->getSettlement() != null) {
                foreach ($blueprints as $blueprint) {
                    $region->addResourceDeposit($blueprint, 1);
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
        ];
    }
}