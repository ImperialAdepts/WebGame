<?php
namespace PlanetBundle\Form;

use PlanetBundle\Entity\Resource\Blueprint;

class BlueprintAdapter
{
    /** @var int */
    private $blueprintId;
    /** @var string */
    private $name = '';
    /** @var string[] */
    private $traits = [];

    /**
     * BlueprintAdapter constructor.
     * @param Blueprint $blueprint
     */
    public function __construct(Blueprint $blueprint = null)
    {
        if ($blueprint != null) {
            $this->setName($blueprint->getDescription());
            $this->setTraits($blueprint->getTraitValues());
            $this->setBlueprintId($blueprint->getId());
        }
    }

    public function setIntoEntity(Blueprint $blueprint) {
        $blueprint->setDescription($this->getName());
        $blueprint->setTraitValues($this->getTraits());
    }

    /**
     * @return int
     */
    public function getBlueprintId()
    {
        return $this->blueprintId;
    }

    /**
     * @param int $blueprintId
     */
    public function setBlueprintId($blueprintId)
    {
        $this->blueprintId = $blueprintId;
    }

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
}