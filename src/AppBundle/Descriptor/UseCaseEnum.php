<?php
namespace AppBundle\Descriptor;

use AppBundle\Descriptor\Adapters\EnergySource;
use AppBundle\Descriptor\Adapters\LandBuilding;
use AppBundle\Descriptor\Adapters\LivingBuilding;
use AppBundle\Descriptor\Adapters\Team;
use AppBundle\Descriptor\Adapters\TeamBuilder;
use AppBundle\Descriptor\Adapters\TeamFarmer;
use AppBundle\Descriptor\Adapters\TeamMerchant;
use AppBundle\Descriptor\Adapters\TeamScientist;
use AppBundle\Descriptor\Adapters\TeamTransporter;
use AppBundle\Descriptor\Adapters\TeamWorker;

/**
 * Odpovida ID UseCase v xml s technologiemi
 */
class UseCaseEnum
{
	const ADMINISTRATIVE_DISTRICT = 'administrative_district';
	const LAND_BUILDING = 'land_building';
	const PORTABLES = 'portables';
	const RESOURCE_DEPOSIT = 'resource_deposit';
	const ENERGY_SOURCE = 'energy_source';
	const ENERGY_CONSUMER = 'energy_consumer';
	const ENERGY_DEPOSIT = 'energy_deposit';
	const TRANSPORT_VEHICLE = 'transport_vehicle';
	const CONTROL_UNIT = 'control_unit';
	const LIVING_BUILDINGS = 'living_buildings';
	const ELECTRIC_PLANT = 'electric_plant';
	const PORTABLE_GENERATOR = 'portable_generator';
	const FACTORY = 'factory';
	const DEEP_GROUND_MINE = 'deep_ground_mine';
	const TOOL = 'tool';
	const BASIC_FOOD = 'basic_food';
	const TEAM = 'team';
	const TEAM_TRANSPORTERS = 'transport_team';
	const TEAM_BUILDERS = 'builder_team';
	const TEAM_SCIENTISTS = 'science_team';
	const TEAM_MERCHANTS = 'merchant_team';
	const TEAM_WORKERS = 'work_team';
	const TEAM_FARMERS = 'farm_team';

    public static $useCaseToAdapterMap = [
	    self::LIVING_BUILDINGS => LivingBuilding::class,
        self::LAND_BUILDING => LandBuilding::class,
        self::ENERGY_SOURCE => EnergySource::class,
        self::TEAM_BUILDERS => TeamBuilder::class,
        self::TEAM_TRANSPORTERS => TeamTransporter::class,
        self::TEAM_WORKERS => TeamWorker::class,
        self::TEAM_SCIENTISTS => TeamScientist::class,
        self::TEAM_MERCHANTS => TeamMerchant::class,
        self::TEAM_FARMERS => TeamFarmer::class,
        self::TEAM => Team::class,
    ];

	public static function getAdapter($useCaseName) {
	    if (isset(self::$useCaseToAdapterMap[$useCaseName])) {
	        return self::$useCaseToAdapterMap[$useCaseName];
        } else {
	        return null;
        }
    }

    public static function getAdapterUseCase($adapterClass) {
	    $flippedArray = array_flip(self::$useCaseToAdapterMap);
        if (isset($flippedArray[$adapterClass])) {
            return $flippedArray[$adapterClass];
        } else {
            return null;
        }
    }
}