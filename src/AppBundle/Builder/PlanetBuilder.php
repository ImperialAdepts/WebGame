<?php
namespace AppBundle\Builder;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Entity;
use AppBundle\Fixture\ResourceAndBlueprintFixture;
use Doctrine\ORM\EntityManager;

class PlanetBuilder
{
	// TODO: predelat do rozumnejsiho configu
	const STEP_DAY_COUNT = 0.5;

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
		$settlement->setType($project->getBuildingBlueprint());
		$settlement->setRegions([$project->getRegion()]);
		$settlement->setOwner($project->getSupervisor());
		$settlement->setManager($project->getSupervisor());
		$project->getRegion()->setSettlement($settlement);
		$this->entityManager->persist($settlement);
		$this->entityManager->persist($project->getRegion());
	}

	public function buildProjectStep(Entity\Planet\BuildingProject $project)
	{
		$resourceSettlements = $this->entityManager->getRepository(Entity\Planet\Settlement::class)->getByHumanSupervisor($project->getSupervisor());
		$project->setSteplogs([]);

		$mandays = [];
		/** @var Entity\Planet\Settlement $settlement */
		foreach ($resourceSettlements as $settlement) {
			$people = $settlement->getResourceDeposit(ResourceDescriptorEnum::PEOPLE);
			if ($people) {
				$project->addSteplog("Settlement #{$settlement->getId()} has {$people->getAmount()} people");
				$mandays[$settlement->getId()] = $people->getAmount() * self::STEP_DAY_COUNT;
			} else {
				$project->addSteplog("There is no people in Settlement #{$settlement->getId()} there are only that:");
				foreach ($settlement->getResourceDeposits() as $r => $deposit) {
					$project->addSteplog("...{$deposit->getAmount()} of {$deposit->getResourceDescriptor()}/key:$r");
				}
				$mandays[$settlement->getId()] = 0;
			}
		}

		$missingResources = array_keys($project->getMissingResources());
		foreach ($missingResources as $resource) {
			$project->addSteplog("Finding $resource...");
			/** @var Entity\Planet\Settlement $settlement */
			foreach ($resourceSettlements as $settlement) {
				$missingResource = $project->getMissingResource($resource);
				$project->addSteplog("Finding amount $missingResource of $resource in Settlement #{$settlement->getId()}");

				if ($resource == ResourceDescriptorEnum::MANDAY) {
					$storedAmount = $mandays[$settlement->getId()];
					$project->addSteplog("There is $storedAmount mandays");
					if ($storedAmount > $missingResource) {
						$mandays[$settlement->getId()] = $storedAmount - $missingResource;
						$project->setMissingResource($resource, 0);
						$this->entityManager->persist($settlement);
						break;
					}
					if ($storedAmount <= $missingResource) {
						$mandays[$settlement->getId()] = 0;
						$project->setMissingResource($resource, $missingResource - $storedAmount);
					}
					continue;
				} elseif ($settlement->getResourceDeposit($resource) == null) {
					$project->addSteplog("There is no $resource in Settlement #{$settlement->getId()} there are only that:");
					foreach ($settlement->getResourceDeposits() as $r => $deposit) {
						$project->addSteplog("...{$deposit->getAmount()} of {$deposit->getResourceDescriptor()}/$r");
					}
					continue;
				} else {
					$storedAmount = $settlement->getResourceDeposit($resource)->getAmount();
					$project->addSteplog("There is $storedAmount of $resource");
					if ($storedAmount > $missingResource) {
						$settlement->getResourceDeposit($resource)->setAmount($storedAmount - $missingResource);
						$project->setMissingResource($resource, 0);
						$this->entityManager->persist($settlement);
						break;
					}
					if ($storedAmount <= $missingResource) {
						$this->entityManager->remove($settlement->getResourceDeposit($resource));
						$project->setMissingResource($resource, $missingResource - $storedAmount);
					}
				}
			}
		}
	}

	public function newColony(Entity\Planet\Region $region, Entity\Human $human)
	{
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
		$warehouses->setBlueprint($this->getBlueprint(ResourceAndBlueprintFixture::WAREHOUSE_BLUEPRINT));
		$warehouses->setSettlement($settlement);
		$this->entityManager->persist($warehouses);

		$ironPlateDeposit = new Entity\ResourceDeposit();
		$ironPlateDeposit->setAmount(2000);
		$ironPlateDeposit->setResourceDescriptor(ResourceDescriptorEnum::IRON_PLATE);
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