<?php

namespace AppBundle\Entity\rpg;

class KnowledgeTypeEnum
{
    const BUILDING_SIMPLE = 'building_simple';
    const BUILDING_SKYSCRAPER = 'building_skyscraper';
    const BUILDING_HIVE = 'building_hive';
    const BUILDING_INFRASTRUCTURE = 'building_infrastructure';
    const BUILDING_TRANSPORT_VEHICLES = 'building_transport_vehicles';
    const BUILDING_SPACE = 'building_space';
    const BUILDING_SPACE_STATION = 'building_space_station';
    const BUILDING_SPACE_SHIP = 'building_space_ship';
    const PRODUCTION_HANDMADE = 'production_handmade';
    const PRODUCTION_AUTOMATIC = 'production_automatic';
    const PRODUCTION_MINING = 'production_mining';
    const MANAGEMENT_SETTLEMENT = 'management_settlement';
    const MANAGEMENT_VASALISE = 'management_vasalise';
    const MANAGEMENT_TREATY = 'management_treaty';

    /**
     * @param $soulPreferenceType
     * @return string[] what you must have
     */
    public static function getPreferenceDependencies($soulPreferenceType) {
        return [];
    }
}