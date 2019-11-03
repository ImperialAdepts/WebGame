<?php

namespace PlanetBundle\Entity\Resource;

use PlanetBundle\Entity\Resource\BlueprintRecipe;
use Doctrine\ORM\Mapping as ORM;

trait BlueprintRecipeDependencyTrait
{
    /**
     * @var BlueprintRecipe
     *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Resource\BlueprintRecipe")
     * @ORM\JoinColumn(name="blueprint_recipe_id", referencedColumnName="id", nullable=false)
     */
    private $blueprintRecipe;

    /**
     * @return BlueprintRecipe
     */
    public function getBlueprintRecipe()
    {
        return $this->blueprintRecipe;
    }

    /**
     * @param BlueprintRecipe $blueprintRecipe
     */
    public function setBlueprintRecipe(BlueprintRecipe $blueprintRecipe)
    {
        $this->blueprintRecipe = $blueprintRecipe;
    }

}

