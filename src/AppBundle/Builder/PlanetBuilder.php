<?php
namespace AppBundle\Builder;

use AppBundle\Entity\Human;
use AppBundle\Entity\Human\Title;
use AppBundle\PlanetConnection\DynamicPlanetConnector;
use Doctrine\Common\Persistence\ObjectManager;
use PlanetBundle\Concept\ColonizationShip;
use PlanetBundle\Entity as PlanetEntity;
use Doctrine\ORM\EntityManager;

class PlanetBuilder
{
	// TODO: predelat do rozumnejsiho configu
	const STEP_DAY_COUNT = 0.5;

    /** @var ObjectManager */
    private $generalEntityManager;
	/** @var ObjectManager */
	private $planetEntityManager;

    /**
     * PlanetBuilder constructor.
     * @param ObjectManager $generalEntityManager
     * @param ObjectManager $planetEntityManager
     */
    public function __construct(ObjectManager $generalEntityManager, ObjectManager $planetEntityManager)
    {
        $this->generalEntityManager = $generalEntityManager;
        $this->planetEntityManager = $planetEntityManager;
    }

    public function buildProject(PlanetEntity\BuildingProject $project)
	{
        $project->getRegion()->addResourceDeposit($project->getBuildingBlueprint(), 1);
		$this->planetEntityManager->persist($project->getRegion());
	}

	public function buildProjectStep(PlanetEntity\CurrentBuildingProject $project)
	{
		$resourceSettlements = $this->planetEntityManager->getRepository(PlanetEntity\Settlement::class)->getByHumanSupervisor($project->getSupervisor());
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
				foreach ($region->getResources() as $r => $deposit) {
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
						$this->planetEntityManager->persist($region);
						break;
					}
					if ($storedAmount <= $missingResource) {
						$mandays[$region->getCoords()] = 0;
						$project->setMissingResource($resource, $missingResource - $storedAmount);
					}
					continue;
				} elseif ($region->getResourceDeposit($resource) == null) {
					$project->addNotification("There is no $resource in Region #{$region->getCoords()} there are only that:");
					foreach ($region->getResources() as $r => $deposit) {
						$project->addNotification("...{$deposit->getAmount()} of {$deposit->getResourceDescriptor()}/$r");
					}
					continue;
				} else {
					$storedAmount = $region->getResourceDeposit($resource)->getAmount();
					$project->addNotification("There is $storedAmount of $resource");
					if ($storedAmount > $missingResource) {
						$region->getResourceDeposit($resource)->setAmount($storedAmount - $missingResource);
						$project->setMissingResource($resource, 0);
						$this->planetEntityManager->persist($region);
						break;
					}
					if ($storedAmount <= $missingResource) {
						$this->planetEntityManager->remove($region->getResourceDeposit($resource));
						$project->setMissingResource($resource, $missingResource - $storedAmount);
					}
				}
			}
		}
	}

    /**
     * @param PlanetEntity\Peak $administrativeCenter
     * @param PlanetEntity\Human $human
     * @param PlanetEntity\Deposit $colonizationPackageDeposit
     */
	public function newColony(PlanetEntity\Peak $administrativeCenter, PlanetEntity\Human $human, PlanetEntity\Deposit $colonizationPackageDeposit)
	{
	    $regions = $this->planetEntityManager->getRepository(PlanetEntity\Region::class)->findPeakSurrounding($administrativeCenter);
	    $settlement = $this->createSettlement($regions, $administrativeCenter, $human);

        foreach ($colonizationPackageDeposit->getResourceDescriptors() as $resourceDescriptor) {
            $resourceCopy = clone $resourceDescriptor;
            $settlement->getDeposit()->addResourceDescriptors($resourceCopy);
        }
	}

	public function createSettlement(array $regions, PlanetEntity\Peak $administrativeCenter, PlanetEntity\Human $human) {
        $globalHuman = $this->generalEntityManager->find(Human::class, $human->getGlobalHumanId());

        $settlement = new PlanetEntity\Settlement();
        $settlement->setType('village');
        $settlement->setRegions($regions);
        $settlement->setAdministrativeCenter($administrativeCenter);
        $settlement->setOwner($human);
        $settlement->setManager($human);
        $this->planetEntityManager->persist($settlement);
        $this->planetEntityManager->flush($settlement);

        $protectorTitle = new Human\SettlementTitle();
        $protectorTitle->setName('Protector of '.$settlement->getName());
        $protectorTitle->setHumanHolder($globalHuman);
        $protectorTitle->setTransferSettings([
            'inheritance' => 'primogeniture',
        ]);
        $protectorTitle->setSettlementId($settlement->getId());
        $protectorTitle->setSettlementPlanet(DynamicPlanetConnector::$PLANET);
        $this->generalEntityManager->persist($protectorTitle);

        $globalHuman->getTitles()->add($protectorTitle);
        $globalHuman->setTitle($protectorTitle);

        $administrativeCenter->setSettlement($settlement);
        $this->planetEntityManager->persist($administrativeCenter);

        /** @var PlanetEntity\Region $region */
        foreach ($regions as $region) {
            $region->setSettlement($settlement);
            $this->planetEntityManager->persist($region);
        }

        return $settlement;
    }

	public function getAvailableBlueprints(PlanetEntity\Resource\DepositInterface $deposit, PlanetEntity\Human $human) {
	    // TODO: overit ze dotycny vlastni blueprinty
        $availables = [];
        $recipes = $this->planetEntityManager->getRepository(PlanetEntity\Resource\BlueprintRecipe::class)->findAll();
        /** @var PlanetEntity\Resource\BlueprintRecipe $recipe */
        foreach ($recipes as $recipe) {
            foreach ($recipe->getInputs() as $blueprintId => $amount) {
                $blueprint = $this->planetEntityManager->getRepository(PlanetEntity\Resource\Blueprint::class)->find($blueprintId);
                if ($blueprint == null) {
                    continue;
                }
                $resourceDeposits = $deposit->filterByBlueprint($blueprint);
                if (PlanetEntity\Deposit::sumAmounts($resourceDeposits) < $amount) {
                    continue 2;
                }
            }
            foreach ($recipe->getTools() as $blueprintId => $amount) {
                $blueprint = $this->planetEntityManager->getRepository(PlanetEntity\Resource\Blueprint::class)->find($blueprintId);
                if ($blueprint == null) {
                    continue;
                }
                $resourceDeposits = $deposit->filterByBlueprint($blueprint);
                if (PlanetEntity\Deposit::sumAmounts($resourceDeposits) < $amount) {
                    continue 2;
                }
            }
            $availables[] = $recipe;
        }
        return $availables;
    }

	private function getBlueprint($name)
	{
		return $this->planetEntityManager->getRepository(PlanetEntity\Resource\Blueprint::class)->getByName($name);
	}

}