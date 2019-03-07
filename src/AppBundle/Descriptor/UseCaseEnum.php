<?php
namespace AppBundle\Descriptor;

use AppBundle\Descriptor\Adapters\EnergySource;
use AppBundle\Descriptor\Adapters\LandBuilding;
use AppBundle\Descriptor\Adapters\LivingBuilding;

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

	public static $useCaseToAdapterMap = [
	    self::LIVING_BUILDINGS => LivingBuilding::class,
        self::LAND_BUILDING => LandBuilding::class,
        self::ENERGY_SOURCE => EnergySource::class,
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