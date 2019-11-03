<?php
namespace PlanetBundle\Concept;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use PlanetBundle\Annotation\Concept\Changeble;
use PlanetBundle\Annotation\Concept\CreationSource;
use PlanetBundle\Annotation\Concept\DependentInformation;
use PlanetBundle\Annotation\Concept\InstallationDifficulty;
use PlanetBundle\Annotation\Concept\Part;
use PlanetBundle\Annotation\Concept\Persistent;
use PlanetBundle\Entity\Resource\Blueprint;
use PlanetBundle\Entity\Resource\BlueprintRecipe;
use Symfony\Component\Debug\Debug;
use Tracy\Debugger;

class ConceptToBlueprintRecipe
{
    public static function getStructure(Blueprint $blueprint, $conceptClass) {
        $recipeStructure = [];
        $reader = new AnnotationReader();

        $reflectionClass = new \ReflectionClass($conceptClass);
        foreach ($reflectionClass->getProperties() as $prop) {
            /** @var InstallationDifficulty $installationAnnotation */
            $installationAnnotation = $reader->getPropertyAnnotation($prop, InstallationDifficulty::class);

            if ($installationAnnotation != null) {
                $recipeStructure['installation'][$prop->getName()] = $installationAnnotation->getWorkHours();
            }
        }

        foreach ($reader->getClassAnnotation($reflectionClass, CreationSource::class) as $source) {
            $recipeStructure['sources'][] = $source->value;
        }

        if ($reflectionClass->getParentClass() != null) {
            $recipeStructure = array_merge($recipeStructure, self::getStructure($blueprint, $reflectionClass->getParentClass()->getName()));
        }

        return $recipeStructure;
    }


}