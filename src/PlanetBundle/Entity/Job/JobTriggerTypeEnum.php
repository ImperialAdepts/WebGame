<?php
namespace PlanetBundle\Entity\Job;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class JobTriggerTypeEnum extends Type
{
    const PHASE_START = 'phase_start';
    const PHASE_END = 'phase_end';
    const EVENT_TRIGGER = 'event_trigger';

    private static $types = [
        self::PHASE_START,
        self::PHASE_END,
        self::EVENT_TRIGGER,
    ];

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "ENUM('".implode("', '", self::$types)."')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!in_array($value, self::$types)) {
            throw new \InvalidArgumentException("Invalid type");
        }
        return $value;
    }

    public function getName()
    {
        return 'job_triggertype_enum';
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}