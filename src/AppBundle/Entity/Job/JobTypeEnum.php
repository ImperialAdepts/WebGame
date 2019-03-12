<?php
namespace AppBundle\Job;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class JobTypeEnum extends Type
{
    const BUILD = 'build';
    const TRANSPORT = 'move';
    const PRODUCE = 'produce';
    const SELL = 'sell';
    const BUY = 'buy';

    private static $types = [
        self::BUILD,
        self::TRANSPORT,
        self::PRODUCE,
        self::SELL,
        self::BUY,
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
        return 'jobtypeenum';
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}