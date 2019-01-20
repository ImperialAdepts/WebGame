<?php
namespace AppBundle\Fixture;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Entity;

class ResourceAndBlueprintFixture extends \Doctrine\Bundle\FixturesBundle\Fixture
{
	// tohle je potreba jeste nekde jinde aby slo dohledat zakladni blueprinty
	const IRON_PLATE_BLUEPRINT = 'Basic plate';
	const OIL_BARREL_BLUEPRINT = 'Basic barrel';
	const MINE_BLUEPRINT = 'Basic mine';
	const FURNACE_BLUEPRINT = 'Blast furnace';
	const VILLAGE_BLUEPRINT = 'Basic village';
	const LAB_BLUEPRINT = 'Basic lab';
	const FARM_BLUEPRINT = 'Basic farm';
	const WAREHOUSE_BLUEPRINT = 'Container warehouse';
    const MINE_DISTRICT_BLUEPRINT = 'Mine district upgrade';
    const LAB_DISTRICT_BLUEPRINT = 'Laboratory district upgrade';
    const FARM_DISTRICT_BLUEPRINT = 'Farm district upgrade';

	/**
	 * Load data fixtures with the passed EntityManager
	 *
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(\Doctrine\Common\Persistence\ObjectManager $manager)
	{
	    // --------------- RESOURCES
		$ironPlateBlueprint = $this->createBlueprint(self::IRON_PLATE_BLUEPRINT, ResourceDescriptorEnum::IRON_PLATE, [
			ResourceDescriptorEnum::MANDAY => 0.5,
			ResourceDescriptorEnum::IRON_ORE => 2,
		]);
		$manager->persist($ironPlateBlueprint);
		$oilBarrelBlueprint = $this->createBlueprint(self::OIL_BARREL_BLUEPRINT, ResourceDescriptorEnum::OIL_BARREL, [
			ResourceDescriptorEnum::MANDAY => 0.3,
			ResourceDescriptorEnum::IRON_PLATE => 3,
		]);
		$manager->persist($oilBarrelBlueprint);

        // --------------- BUILDINGS
		$mineBlueprint = $this->createBlueprint(self::MINE_BLUEPRINT, ResourceDescriptorEnum::MINE, [
			ResourceDescriptorEnum::MANDAY => 10,
			ResourceDescriptorEnum::IRON_PLATE => 300,
		], [
            ResourceDescriptorEnum::VILLAGE => 1,
        ]);
        $manager->persist($mineBlueprint);
        $furnaceBlueprint = $this->createBlueprint(self::FURNACE_BLUEPRINT, ResourceDescriptorEnum::FURNACE, [
            ResourceDescriptorEnum::MANDAY => 1000,
            ResourceDescriptorEnum::IRON_PLATE => 500,
        ], [
            ResourceDescriptorEnum::RESOURCE_DISTRICT => 1,
        ]);
		$manager->persist($furnaceBlueprint);
        $labBlueprint = $this->createBlueprint(self::LAB_BLUEPRINT, ResourceDescriptorEnum::LABORATORY, [
            ResourceDescriptorEnum::MANDAY => 500,
            ResourceDescriptorEnum::IRON_PLATE => 200,
        ], [
            ResourceDescriptorEnum::VILLAGE => 1,
        ]);
        $manager->persist($labBlueprint);
        $farmBlueprint = $this->createBlueprint(self::FARM_BLUEPRINT, ResourceDescriptorEnum::FARM, [
            ResourceDescriptorEnum::MANDAY => 30,
        ], [
            ResourceDescriptorEnum::VILLAGE => 1,
        ]);
        $manager->persist($farmBlueprint);
        $warehouse = $this->createBlueprint(self::WAREHOUSE_BLUEPRINT, ResourceDescriptorEnum::WAREHOUSE, [
            ResourceDescriptorEnum::MANDAY => 10,
            ResourceDescriptorEnum::IRON_PLATE => 10,
        ], [
            ResourceDescriptorEnum::VILLAGE => 1,
        ]);
        $manager->persist($warehouse);

        // --------------- SETTLEMENTS
		$villageBlueprint = $this->createBlueprint(self::VILLAGE_BLUEPRINT, ResourceDescriptorEnum::VILLAGE, [
			ResourceDescriptorEnum::MANDAY => 100,
			ResourceDescriptorEnum::IRON_PLATE => 20,
		]);
		$manager->persist($villageBlueprint);
        $resourceDistrictBlueprint = $this->createBlueprint(self::MINE_DISTRICT_BLUEPRINT, ResourceDescriptorEnum::RESOURCE_DISTRICT, [
            ResourceDescriptorEnum::MANDAY => 10,
            ResourceDescriptorEnum::IRON_PLATE => 1,
        ], [
            ResourceDescriptorEnum::VILLAGE => 1,
        ]);
        $manager->persist($resourceDistrictBlueprint);
        $farmDBlueprint = $this->createBlueprint(self::FARM_DISTRICT_BLUEPRINT, ResourceDescriptorEnum::FARM_DISTRICT, [
            ResourceDescriptorEnum::MANDAY => 10,
            ResourceDescriptorEnum::IRON_PLATE => 1,
        ], [
            ResourceDescriptorEnum::VILLAGE => 1,
        ]);
        $manager->persist($farmDBlueprint);
        $labDBlueprint = $this->createBlueprint(self::LAB_DISTRICT_BLUEPRINT, ResourceDescriptorEnum::LABORATORY_DISTRICT, [
            ResourceDescriptorEnum::MANDAY => 10,
            ResourceDescriptorEnum::IRON_PLATE => 1,
        ], [
            ResourceDescriptorEnum::VILLAGE => 1,
        ]);
		$manager->persist($labDBlueprint);


		$manager->flush();

		$builder = new \AppBundle\Builder\PlanetBuilder($manager);
		$humans = $manager->getRepository(Entity\Human::class)->findAllIncarnated();
		$regions = $manager->getRepository(Entity\Planet\Region::class)->findAll();
		$regionCounter = 1;
		/** @var Entity\Human $human */
        foreach ($humans as $human) {
            /** @var Entity\Planet\Region $centralRegion */
            $centralRegion = $regions[$regionCounter];
            $regionCounter += 4;
			$builder->newColony($centralRegion, $human);
			$human->setCurrentPosition($centralRegion->getSettlement());
		}

		$manager->flush();
	}

	private function createBlueprint($name, $resource, array $requirements = [], array $constraints = [])
	{
		$blueprint = new Entity\Blueprint();
		$blueprint->setDescription($name);
		$blueprint->setResourceDescriptor($resource);
		$blueprint->setRequirements($requirements);
		$blueprint->setConstraints($constraints);
		$blueprint->setSpace(1);
		$blueprint->setWeight(1);
		return $blueprint;
	}
}