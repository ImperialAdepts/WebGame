<?php

namespace AppBundle\Entity\rpg;

use AppBundle\EnumAlignmentType;

class SoulPreferenceTypeEnum
{
    const GOOD_EMPATHY = 'good_empathy';
    const GOOD_TRADER = 'good_trader';
    const GOOD_SMALL_WORLD = 'good_small_world';
    const GOOD_LIFE_MATTERS = 'good_life_matters';
    const GOOD_NO_SECRETS = 'good_no_secrets';
    const GOOD_UNBREAKABLE_DEAL = 'good_unbreakable_deal';
    const MORAL_NEUTRAL_POWER_EQUILIBRIUM = 'moral_neutral_power_equilibrium';
    const MORAL_NEUTRAL_SIZE_EQUILIBRIUM = 'moral_neutral_size_equilibrium';
    const MORAL_NEUTRAL_WEALTH_EQUILIBRIUM = 'moral_neutral_wealth_equilibrium';
    const EVERYBODY_ONLY_FRIENDS = 'everybody_only_friends';
    const EVERYBODY_WHAT_I_SEE = 'everybody_what_i_see';
    const MORAL_NEUTRAL_LONG_TERM = 'moral_neutral_long_term';
    const MORAL_NEUTRAL_SHORT_TERM = 'moral_neutral_short_term';
    const EVIL_SELFISH = 'evil_selfish';
    const EVIL_GENERAL = 'evil_general';
    const EVIL_CONQUISTADOR = 'evil_conquistador';
    const EVIL_ODINIST = 'evil_odinist';
    const EVIL_KING = 'evil_king';
    const EVIL_NEMESIS = 'evil_nemesis';
    const EVIL_LIFE_NOT_MATTER = 'evil_life_not_matter';
    const EVIL_MONUMENTALIST = 'evil_monumentalist';
    const EVIL_WALLSTREET = 'evil_wallstreet';
    const EVIL_BETTER_THAN_RIVAL = 'evil_better_than_rival';
    const EVIL_BETTER_THAN_ALLY = 'evil_better_than_ally';
    const EVIL_BEST = 'evil_best';

    /**
     * @param $soulPreferenceType
     * @return string[] what you must have
     */
    public static function getPreferenceDependencies($soulPreferenceType) {
        return [];
    }

    /**
     * @param $soulPreferenceType
     * @return string[] who can have it, empty => nobody
     */
    public static function getAlignmentDependencies($soulPreferenceType) {
        return [
            EnumAlignmentType::LAWFUL_NEUTRAL,
            EnumAlignmentType::LAWFUL_GOOD,
            EnumAlignmentType::LAWFUL_EVIL,
            EnumAlignmentType::CHAOTIC_NEUTRAL,
            EnumAlignmentType::CHAOTIC_GOOD,
            EnumAlignmentType::CHAOTIC_NEUTRAL,
            EnumAlignmentType::NEUTRAL_NEUTRAL,
            EnumAlignmentType::NEUTRAL_GOOD,
            EnumAlignmentType::NEUTRAL_EVIL,
        ];
    }
}