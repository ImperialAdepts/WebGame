<?php
namespace AppBundle\Descriptor;

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

	public static function getAdapter($useCaseName) {
	    switch ($useCaseName) {
            case self::LIVING_BUILDINGS: return LivingBuilding::class;
            case self::LAND_BUILDING: return LandBuilding::class;
            default: return null;
        }
    }
}