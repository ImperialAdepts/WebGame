<?php
namespace PlanetBundle\Form;

use PlanetBundle\Entity\Resource\Blueprint;

class BlueprintDTO
{
    /** @var string */
    private $name;
    /** @var string */
    private $concept;

    /** @var string[] */
    private $traits;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getConcept()
    {
        return $this->concept;
    }

    /**
     * @param string $concept
     */
    public function setConcept($concept)
    {
        $this->concept = $concept;
    }

    /**
     * @return string[]
     */
    public function getTraits()
    {
        return $this->traits;
    }

    /**
     * @param string[] $traits
     */
    public function setTraits($traits)
    {
        $this->traits = $traits;
    }

    public function getTraitValue($traitName) {
        if (isset($this->traits[$traitName])) {
            return $this->traits[$traitName];
        }
        return null;
    }

    public function setTraitValue($traitName, $value) {
        $this->traits[$traitName] = $value;
    }

    public function __get($name)
    {
        return $this->getTraitValue($name);
    }

    public function __set($name, $value)
    {
        $this->setTraitValue($name, $value);
    }

    public function getBlueprintEntity() {
        $blueprint = new Blueprint();
        $blueprint->setDescription($this->getName());
        $blueprint->setConcept($this->getConcept());
        $blueprint->setTraitValues($this->getTraits());
        return $blueprint;
    }
}