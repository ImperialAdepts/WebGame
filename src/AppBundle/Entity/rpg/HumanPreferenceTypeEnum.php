<?php

namespace AppBundle\Entity\rpg;

class HumanPreferenceTypeEnum
{
    const FAMILY_OPINION = 'family_opinion';
    const FAMILY_OWN_CHILDREN = 'family_own_children';
    const SAME_LOCATION_BONDING = 'same_location_bonding';
    const EMPATHY = 'empathy';
    const FORTUNE_SENSE = 'fortune_sense';
    const VIOLENCE = 'violence';
    const KNOWLEDGE_VALUE = 'knowledge_value';

    /**
     * @param $humanPreferenceType
     * @return string[] what you must have
     */
    public static function getPreferenceDependencies($humanPreferenceType) {
        return [];
    }

    public static function getPreferenceValues($humanPreferenceType) {
        switch ($humanPreferenceType) {
            case self::KNOWLEDGE_VALUE: return [
                200 => 'STRONG',
                100 => 'DEFAULT',
                50 => 'WEAK',
                0 => 'NONE',
                -80 => 'CONTEMPT',
            ];
            default: return [
                200 => 'STRONG',
                100 => 'DEFAULT',
                50 => 'WEAK',
                0 => 'NONE',
            ];
        }
    }

}