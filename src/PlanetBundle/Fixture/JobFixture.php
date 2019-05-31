<?php
namespace PlanetBundle\Fixture;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Entity as GeneralEntity;
use AppBundle\Fixture\PlanetsFixture;
use PlanetBundle\Builder\RegionTerrainTypeEnumBuilder;
use PlanetBundle\Entity as PlanetEntity;
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
     * @param \Doctrine\Common\Persistence\ObjectManager $generalManager
     * @throws \Exception
     */
	public function load(\Doctrine\Common\Persistence\ObjectManager $generalManager)
	{
        echo __CLASS__."\n";

        /** @var GeneralEntity\SolarSystem\Planet $planets */
        $planets = $generalManager->getRepository(GeneralEntity\SolarSystem\Planet::class)->findAll();
        /** @var GeneralEntity\SolarSystem\Planet $planet */
        foreach ($planets as $planet) {
            echo "Planet ".$planet->getName().": ";
            if ($planet->getDatabaseCredentials() == null) {
                echo "skipped\n";
                continue;
            }

            $this->container->get('dynamic_planet_connector')->setPlanet($planet, true);
            $manager = $this->container->get('doctrine')->getManager('planet');

            $farmingBlueprints = $manager->getRepository(PlanetEntity\Blueprint::class)->getByUseCase(UseCaseEnum::TYPE_FARMING);
            $productionBlueprints = $manager->getRepository(PlanetEntity\Blueprint::class)->getByUseCase(UseCaseEnum::TYPE_PRODUCTION);
            $settlements = $manager->getRepository(PlanetEntity\Settlement::class)->findAll();
            /** @var PlanetEntity\Settlement $settlement */
            foreach ($settlements as $settlement) {
                foreach ($farmingBlueprints as $blueprint) {
                    $farmingJob = new PlanetEntity\Job\ProduceJob();
                    $farmingJob->setRegion($settlement->getMainRegion());
                    $farmingJob->setAmount(4);
                    $farmingJob->setRepetition(null);
                    $farmingJob->setBlueprint($blueprint);
                    $farmingJob->setTriggerType(PlanetEntity\Job\JobTriggerTypeEnum::PHASE_END);
                    $manager->persist($farmingJob);
                }
                foreach ($productionBlueprints as $blueprint) {
                    $productionJob = new PlanetEntity\Job\ProduceJob();
                    $productionJob->setRegion($settlement->getMainRegion());
                    $productionJob->setAmount(4);
                    $productionJob->setRepetition(null);
                    $productionJob->setBlueprint($blueprint);
                    $productionJob->setTriggerType(PlanetEntity\Job\JobTriggerTypeEnum::PHASE_END);
                    $manager->persist($productionJob);
                }
                foreach ($settlement->getRegions() as $region) {
                    $administrationJob = new PlanetEntity\Job\AdministrationJob();
                    $administrationJob->setSupervisor($settlement->getManager());
                    $administrationJob->setRegion($region);
                    $administrationJob->setAmount(null);
                    $administrationJob->setRepetition(null);
                    $administrationJob->setTriggerType(PlanetEntity\Job\JobTriggerTypeEnum::PHASE_START);
                    $manager->persist($administrationJob);
                }
                $manager->flush();
            }
            echo "done\n";
        }
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
            ResourceAndBlueprintFixture::class,
            TeamFixture::class,
        ];
    }
}