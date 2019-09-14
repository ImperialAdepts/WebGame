<?php
namespace PlanetBundle\Concept;

use Symfony\Component\Debug\Debug;
use Tracy\Debugger;

class ConceptToBlueprintAdapter
{
    /** @var string */
    private $conceptName;

    /**
     * ConceptToBlueprintAdapter constructor.
     * @param string $conceptName
     */
    public function __construct($conceptName)
    {
        $this->conceptName = $conceptName;
    }

    public static function getStructure($conceptClass) {
        $structure = [];

        $reflectionClass = new \ReflectionClass($conceptClass);
        foreach ($reflectionClass->getProperties() as $prop) {
            preg_match_all('|@var (.*?) \*/|', $prop->getDocComment(), $annotations);
            $class = $annotations[1][0];
            if ($class != null && $class !== $conceptClass && !in_array($class, ['int', 'float'])) {
                $structure[$prop->getName()]['class'] = 'PlanetBundle\\'.$class;
            } else {
                $structure[$prop->getName()]['value'] = $prop->getDocComment();
            }
        }

        if ($reflectionClass->getParentClass() != null) {
            $structure = array_merge($structure, self::getStructure($reflectionClass->getParentClass()->getName()));
        }

        return $structure;
    }

    public static function getPartsByUseCase($object, $useCase) {
        $parts = [];

        $reflectionClass = new \ReflectionClass($object);
        foreach ($reflectionClass->getProperties() as $prop) {
            preg_match_all('|@var (.*?) \*/|', $prop->getDocComment(), $annotations);
            $object = $annotations[1][0];
            if ($object != null && in_array($useCase, class_uses($object))) {
                $parts[] = $prop->getValue($object);
            }
        }

        return $parts;
    }
}