<?php
namespace AppBundle\Builder;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Entity;
use PlanetBundle\Entity as PlanetEntity;
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

	public function buildProject(PlanetEntity\BuildingProject $project)
	{
        $project->getRegion()->addResourceDeposit($project->getBuildingBlueprint(), 1);
		$this->entityManager->persist($project->getRegion());
	}

	public function buildProjectStep(PlanetEntity\CurrentBuildingProject $project)
	{
		$resourceSettlements = $this->entityManager->getRepository(PlanetEntity\Settlement::class)->getByHumanSupervisor($project->getSupervisor());
        $regions = [];
        /** @var PlanetEntity\Settlement $settlement */
        foreach ($resourceSettlements as $settlement) {
            foreach ($settlement->getRegions() as $region) {
                $regions[] = $region;
            }
        }
		$mandays = [];
		/** @var PlanetEntity\Region $region */
		foreach ($regions as $region) {
			$people = $region->getResourceDeposit(ResourceDescriptorEnum::PEOPLE);
			if ($people) {
				$project->addNotification("Region #{$region->getCoords()} has {$people->getAmount()} people");
				$mandays[$region->getCoords()] = $people->getAmount() * self::STEP_DAY_COUNT;
			} else {
				$project->addNotification("There is no people in Settlement #{$region->getCoords()} there are only that:");
				foreach ($region->getResourceDeposits() as $r => $deposit) {
					$project->addNotification("...{$deposit->getAmount()} of {$deposit->getResourceDescriptor()}/key:$r");
				}
				$mandays[$region->getCoords()] = 0;
			}
		}

		$missingResources = array_keys($project->getMissingResources());
        /** @var PlanetEntity\Region $region */
        foreach ($regions as $region) {
		    foreach ($missingResources as $resource) {
			    $project->addNotification("Finding $resource...");

				$missingResource = $project->getMissingResource($resource);
				$project->addNotification("Finding amount $missingResource of $resource in Settlement #{$region->getCoords()}");

				if ($resource == ResourceDescriptorEnum::MANDAY) {
					$storedAmount = $mandays[$region->getCoords()];
					$project->addNotification("There is $storedAmount mandays");
					if ($storedAmount > $missingResource) {
						$mandays[$region->getCoords()] = $storedAmount - $missingResource;
						$project->setMissingResource($resource, 0);
						$this->entityManager->persist($region);
						break;
					}
					if ($storedAmount <= $missingResource) {
						$mandays[$region->getCoords()] = 0;
						$project->setMissingResource($resource, $missingResource - $storedAmount);
					}
					continue;
				} elseif ($region->getResourceDeposit($resource) == null) {
					$project->addNotification("There is no $resource in Region #{$region->getCoords()} there are only that:");
					foreach ($region->getResourceDeposits() as $r => $deposit) {
						$project->addNotification("...{$deposit->getAmount()} of {$deposit->getResourceDescriptor()}/$r");
					}
					continue;
				} else {
					$storedAmount = $region->getResourceDeposit($resource)->getAmount();
					$project->addNotification("There is $storedAmount of $resource");
					if ($storedAmount > $missingResource) {
						$region->getResourceDeposit($resource)->setAmount($storedAmount - $missingResource);
						$project->setMissingResource($resource, 0);
						$this->entityManager->persist($region);
						break;
					}
					if ($storedAmount <= $missingResource) {
						$this->entityManager->remove($region->getResourceDeposit($resource));
						$project->setMissingResource($resource, $missingResource - $storedAmount);
					}
				}
			}
		}
	}

	public function newColony(PlanetEntity\Region $region, PlanetEntity\Human $human, $colonizationPack)
	{
		$settlement = new PlanetEntity\Settlement();
		$settlement->setType(ResourceDescriptorEnum::VILLAGE);
		$settlement->setRegions([$region]);
		$settlement->setOwner($human);
		$settlement->setManager($human);
		$region->setSettlement($settlement);
		$this->entityManager->persist($settlement);
		$this->entityManager->persist($region);

		$colonyPack = $this->colonyPacks[$colonizationPack];

		foreach ($this->colonyPacks as $colonyPackName => $colonyPack) {
            foreach ($colonyPack['deposits'] as $resource => $data) {
                $resourceDeposit = new PlanetEntity\ResourceDeposit();
                $resourceDeposit->setAmount($data['amount']);
                $resourceDeposit->setResourceDescriptor($resource);
                if (isset($data['blueprint']) && ($blueprint = $this->getBlueprint($data['blueprint'])) != null) {
                    $resourceDeposit->setBlueprint($blueprint);
                }
                $resourceDeposit->setRegion($settlement->getMainRegion());
                $this->entityManager->persist($resourceDeposit);
            }
        }
	}

	public function getAvailableBlueprints(PlanetEntity\Region $region, PlanetEntity\Human $human) {
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
                $resourceDeposit = $region->getResourceDeposit($resourceType);
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