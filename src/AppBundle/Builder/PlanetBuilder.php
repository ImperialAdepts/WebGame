<?php
namespace AppBundle\Builder;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Entity\Human;
use AppBundle\Entity\Human\Title;
use AppBundle\PlanetConnection\DynamicPlanetConnector;
use Doctrine\Common\Persistence\ObjectManager;
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
	private $colonyPacks;

    /**
     * PlanetBuilder constructor.
     * @param ObjectManager $generalEntityManager
     * @param ObjectManager $planetEntityManager
     * @param array $colonyPacks
     */
    public function __construct(ObjectManager $generalEntityManager, ObjectManager $planetEntityManager, $colonyPacks = [])
    {
        $this->generalEntityManager = $generalEntityManager;
        $this->planetEntityManager = $planetEntityManager;
        $this->colonyPacks = $colonyPacks;
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
				foreach ($region->getDeposit() as $r => $deposit) {
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
					foreach ($region->getDeposit() as $r => $deposit) {
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
     * @param $colonizationPack
     * @throws \Doctrine\ORM\ORMException
     */
	public function newColony(PlanetEntity\Peak $administrativeCenter, PlanetEntity\Human $human, $colonizationPack)
	{
	    $regions = $this->planetEntityManager->getRepository(PlanetEntity\Region::class)->findPeakSurrounding($administrativeCenter);
	    $settlement = $this->createSettlement($regions, $administrativeCenter, $human);

//		$colonyPack = $this->colonyPacks[$colonizationPack];
//
//		foreach ($this->colonyPacks as $colonyPackName => $colonyPack) {
//            foreach ($colonyPack['deposits'] as $resource => $data) {
//                $deposit = new PlanetEntity\PeakDeposit();
//
//                /** @var PlanetEntity\Resource\Blueprint $blueprint */
//                if (isset($data['blueprint']) && ($blueprint = $this->getBlueprint($data['blueprint'])) != null) {
//                    $descriptor = new PlanetEntity\Resource\Thing();
//                    $descriptor->setAmount(isset($data['amount']) ? $data['amount'] : 1);
//                    $descriptor->setBlueprint($blueprint);
//                    $descriptor->setDescription($blueprint->getConcept() . " - " . $blueprint->getDescription());
//                } else {
//                    $descriptor = new PlanetEntity\Resource\Resource();
//                    $descriptor->setType($resource);
//                    $descriptor->setAmount(isset($data['amount']) ? $data['amount'] : 1);
//                }
//                $deposit->setPeak($settlement->getAdministrativeCenter());
//                $descriptor->setDeposit($deposit);
//                $this->planetEntityManager->persist($deposit);
//                $this->planetEntityManager->persist($descriptor);
//            }
//        }
	}

	public function createSettlement(array $regions, PlanetEntity\Peak $administrativeCenter, PlanetEntity\Human $human) {
        $globalHuman = $this->generalEntityManager->find(Human::class, $human->getGlobalHumanId());

        $settlement = new PlanetEntity\Settlement();
        $settlement->setType(ResourceDescriptorEnum::VILLAGE);
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

	public function getAvailableBlueprints(PlanetEntity\Region $region, PlanetEntity\Human $human) {
	    // TODO: overit ze dotycny vlastni blueprinty
        $availables = [];
        $blueprints = $this->planetEntityManager->getRepository(PlanetEntity\Resource\Blueprint::class)->getAll();
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
		return $this->planetEntityManager->getRepository(PlanetEntity\Resource\Blueprint::class)->getByName($name);
	}

}