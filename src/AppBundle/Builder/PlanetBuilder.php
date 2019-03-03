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
	private $colonyPacks;

    /**
     * PlanetBuilder constructor.
     * @param EntityManager $entityManager
     * @param $colonyPacks
     */
	public function __construct(EntityManager $entityManager, $colonyPacks)
	{
		$this->entityManager = $entityManager;
		$this->colonyPacks = $colonyPacks;
	}

	public function buildProject(Entity\Planet\BuildingProject $project)
	{
        /** @var Entity\Planet\Settlement $settlement */
		$settlement = $project->getRegion()->getSettlement();
		$settlement->addResourceDeposit($project->getBuildingBlueprint(), 1);
		$this->entityManager->persist($settlement);
		$this->entityManager->persist($project->getRegion());
	}

	public function buildProjectStep(Entity\Planet\CurrentBuildingProject $project)
	{
		$resourceSettlements = $this->entityManager->getRepository(Entity\Planet\Settlement::class)->getByHumanSupervisor($project->getSupervisor());

		$mandays = [];
		/** @var Entity\Planet\Settlement $settlement */
		foreach ($resourceSettlements as $settlement) {
			$people = $settlement->getResourceDeposit(ResourceDescriptorEnum::PEOPLE);
			if ($people) {
				$project->addNotification("Settlement #{$settlement->getId()} has {$people->getAmount()} people");
				$mandays[$settlement->getId()] = $people->getAmount() * self::STEP_DAY_COUNT;
			} else {
				$project->addNotification("There is no people in Settlement #{$settlement->getId()} there are only that:");
				foreach ($settlement->getResourceDeposits() as $r => $deposit) {
					$project->addNotification("...{$deposit->getAmount()} of {$deposit->getResourceDescriptor()}/key:$r");
				}
				$mandays[$settlement->getId()] = 0;
			}
		}

		$missingResources = array_keys($project->getMissingResources());
		foreach ($missingResources as $resource) {
			$project->addNotification("Finding $resource...");
			/** @var Entity\Planet\Settlement $settlement */
			foreach ($resourceSettlements as $settlement) {
				$missingResource = $project->getMissingResource($resource);
				$project->addNotification("Finding amount $missingResource of $resource in Settlement #{$settlement->getId()}");

				if ($resource == ResourceDescriptorEnum::MANDAY) {
					$storedAmount = $mandays[$settlement->getId()];
					$project->addNotification("There is $storedAmount mandays");
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
					$project->addNotification("There is no $resource in Settlement #{$settlement->getId()} there are only that:");
					foreach ($settlement->getResourceDeposits() as $r => $deposit) {
						$project->addNotification("...{$deposit->getAmount()} of {$deposit->getResourceDescriptor()}/$r");
					}
					continue;
				} else {
					$storedAmount = $settlement->getResourceDeposit($resource)->getAmount();
					$project->addNotification("There is $storedAmount of $resource");
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

	public function newColony(Entity\Planet\Region $region, Entity\Human $human, $colonizationPack)
	{
		$settlement = new Entity\Planet\Settlement();
		$settlement->setType(ResourceDescriptorEnum::VILLAGE);
		$settlement->setRegions([$region]);
		$settlement->setOwner($human);
		$settlement->setManager($human);
		$region->setSettlement($settlement);
		$this->entityManager->persist($settlement);
		$this->entityManager->persist($region);

		$colonyPack = $this->colonyPacks[$colonizationPack];

		foreach ($this->colonyPacks as $colonyPackName => $colonyPack) {
//		    echo "\n<br>Colony pack $colonyPackName";
//		    var_dump($colonyPack);
            foreach ($colonyPack['deposits'] as $resource => $data) {
//                echo "\n<br>\t resource $resource";
                $resourceDeposit = new Entity\ResourceDeposit();
                $resourceDeposit->setAmount($data['amount']);
                $resourceDeposit->setResourceDescriptor($resource);
                if (isset($data['blueprint']) && ($blueprint = $this->getBlueprint($data['blueprint'])) != null) {
                    $resourceDeposit->setBlueprint($blueprint);
                }
                $resourceDeposit->setSettlement($settlement);
                $this->entityManager->persist($resourceDeposit);
            }
        }
	}

	public function getAvailableBlueprints(Entity\Planet\Region $region, Entity\Human $human) {
	    // TODO: overit ze dotycny vlastni blueprinty
        $availables = [];
        $blueprints = $this->entityManager->getRepository(Entity\Blueprint::class)->getAll();
        /** @var Entity\Blueprint $blueprint */
        foreach ($blueprints as $blueprint) {
            foreach ($blueprint->getConstraints() as $resourceType => $amount) {
                if ($resourceType == ResourceDescriptorEnum::VILLAGE
                    || $resourceType == ResourceDescriptorEnum::FARM_DISTRICT
                    || $resourceType == ResourceDescriptorEnum::RESOURCE_DISTRICT
                    || $resourceType == ResourceDescriptorEnum::LABORATORY_DISTRICT) {
                    if ($region->getSettlement() == null) {
                        continue 2;
                    }
                    if ($resourceType == $region->getSettlement()->getType()) {
                        continue;
                    } else {
                        continue 2;
                    }
                }
                if ($region->getSettlement() == null) {
                    continue 2;
                }
                $resourceDeposit = $region->getSettlement()->getResourceDeposit($resourceType);
                if ($resourceDeposit == null || $resourceDeposit->getAmount() < $amount) {
                    continue 2;
                }
            }
            $availables[] = $blueprint;
        }
        return $availables;
    }

	private function getBlueprint($name)
	{
		return $this->entityManager->getRepository(Entity\Blueprint::class)->getByName($name);
	}

}