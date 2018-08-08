<?php
namespace AppBundle\Builder;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Entity;
use AppBundle\Fixture\ResourceAndBlueprintFixture;
use Doctrine\ORM\EntityManager;

class PlanetBuilder
{
	/** @var EntityManager */
	private $entityManager;

	/**
	 * PlanetBuilder constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	public function buildProject(Entity\Planet\BuildingProject $project)
	{
		$settlement = new Entity\Planet\Settlement();
		$settlement->setType($project->getBuilding());
		$settlement->setRegions([$project->getRegion()]);
		$settlement->setOwner($project->getSupervisor());
		$settlement->setManager($project->getSupervisor());
		$project->getRegion()->setSettlement($settlement);
		$this->entityManager->persist($settlement);
		$this->entityManager->persist($project->getRegion());
	}
	
	public function newColony(Entity\Planet\Region $region, Entity\Human $human) {
		$settlement = new Entity\Planet\Settlement();
		$settlement->setType(ResourceDescriptorEnum::VILLAGE);
		$settlement->setRegions([$region]);
		$settlement->setOwner($human);
		$settlement->setManager($human);
		$region->setSettlement($settlement);
		$this->entityManager->persist($settlement);
		$this->entityManager->persist($region);

		$warehouses = new Entity\ResourceDeposit();
		$warehouses->setAmount(10);
		$warehouses->setResourceDescriptor(ResourceDescriptorEnum::WAREHOUSE);
		$warehouses->setSettlement($settlement);
		$this->entityManager->persist($warehouses);

		$ironPlateDeposit = new Entity\ResourceDeposit();
		$ironPlateDeposit->setAmount(2000);
		$ironPlateDeposit->setResourceDescriptor(ResourceAndBlueprintFixture::IRON_PLATE_BLUEPRINT);
		$ironPlateDeposit->setBlueprint($this->getBlueprint(ResourceAndBlueprintFixture::IRON_PLATE_BLUEPRINT));
		$ironPlateDeposit->setSettlement($settlement);
		$this->entityManager->persist($ironPlateDeposit);

		$oilDeposit = new Entity\ResourceDeposit();
		$oilDeposit->setAmount(50000);
		$oilDeposit->setResourceDescriptor(ResourceDescriptorEnum::OIL_BARREL);
		$oilDeposit->setBlueprint($this->getBlueprint(ResourceAndBlueprintFixture::OIL_BARREL_BLUEPRINT));
		$oilDeposit->setSettlement($settlement);
		$this->entityManager->persist($oilDeposit);

		$food = new Entity\ResourceDeposit();
		$food->setAmount(10000);
		$food->setResourceDescriptor(ResourceDescriptorEnum::SIMPLE_FOOD);
		$food->setSettlement($settlement);
		$this->entityManager->persist($food);

		$people = new Entity\ResourceDeposit();
		$people->setAmount(200);
		$people->setResourceDescriptor(ResourceDescriptorEnum::PEOPLE);
		$people->setSettlement($settlement);
		$this->entityManager->persist($people);
	}

	private function getBlueprint($name)
	{
		return $this->entityManager->getRepository(Entity\Blueprint::class)->getByName($name);
	}

}