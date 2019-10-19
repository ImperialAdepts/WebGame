<?php
namespace PlanetBundle\Concept;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use PlanetBundle\Annotation\Concept\Changeble;
use PlanetBundle\Annotation\Concept\Part;
use PlanetBundle\Annotation\Concept\Persistent;
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
        $reader = new AnnotationReader();

        if (!class_exists($conceptClass)) {
            return $structure;
        }

        $reflectionClass = new \ReflectionClass($conceptClass);
        foreach ($reflectionClass->getProperties() as $prop) {
            $persistentAnnotation = $reader->getPropertyAnnotation($prop, Persistent::class);
            $changebleAnnotation = $reader->getPropertyAnnotation($prop, Changeble::class);
            $partAnnotation = $reader->getPropertyAnnotation($prop, Part::class);

            if (($partAnnotation != null && $persistentAnnotation != null)
                || ($partAnnotation != null && $changebleAnnotation != null)
                || ($changebleAnnotation != null && $persistentAnnotation != null)) {
                throw new AnnotationException("There can be only one ConceptAnnotation in {$prop->getDeclaringClass()}#{$prop->getName()}");
            }

            if ($partAnnotation != null) {
                $structure[$prop->getName()]['partClass'] = $partAnnotation->getUseCase();
            }
            if ($persistentAnnotation != null) {
                $structure[$prop->getName()]['blueprintValue'] = $persistentAnnotation->getType();
            }
            if ($changebleAnnotation != null) {
                $structure[$prop->getName()]['descriptorValue'] = $changebleAnnotation->getType();
            }
        }

        if ($reflectionClass->getParentClass() != null) {
            $structure = array_merge($structure, self::getStructure($reflectionClass->getParentClass()->getName()));
        }

        return $structure;
    }

    public static function getPartsByUseCase($object, $useCase) {
        $parts = [];

        $reader = new AnnotationReader();
        $reflectionClass = new \ReflectionClass($object);
        foreach ($reflectionClass->getProperties() as $prop) {
            $partAnnotation = $reader->getPropertyAnnotation($prop, Part::class);
            if ($partAnnotation != null && $object != null && $partAnnotation->getUseCase() == $useCase) {
                $parts[] = $prop->getValue($object);
            }
        }

        return $parts;
    }
}