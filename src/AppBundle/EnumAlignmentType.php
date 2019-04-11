<?php
namespace AppBundle;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class EnumAlignmentType extends Type
{
    const ENUM_ALIGNMENT = 'alignment_enum';
    const LAWFUL_GOOD = 'lawful_good';
    const LAWFUL_NEUTRAL = 'lawful_neutral';
    const LAWFUL_EVIL = 'lawful_evil';
    const NEUTRAL_GOOD = 'neutral_good';
    const NEUTRAL_NEUTRAL = 'neutral_neutral';
    const NEUTRAL_EVIL = 'neutral_evil';
    const CHAOTIC_GOOD = 'chaotic_good';
    const CHAOTIC_NEUTRAL = 'chaotic_neutral';
    const CHAOTIC_EVIL = 'chaotic_evil';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "ENUM('lawful_good', 'lawful_neutral', 'lawful_evil', 'neutral_good', 'neutral_neutral', 'neutral_evil', 'chaotic_good', 'chaotic_neutral', 'chaotic_evil')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!in_array($value, [
            self::LAWFUL_GOOD,
            self::LAWFUL_NEUTRAL,
            self::LAWFUL_EVIL,
            self::NEUTRAL_GOOD,
            self::NEUTRAL_NEUTRAL,
            self::NEUTRAL_EVIL,
            self::CHAOTIC_GOOD,
            self::CHAOTIC_NEUTRAL,
            self::CHAOTIC_EVIL,
        ])) {
            throw new \InvalidArgumentException("Invalid alignment");
        }
        return $value;
    }

    public function getName()
    {
        return self::ENUM_ALIGNMENT;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}