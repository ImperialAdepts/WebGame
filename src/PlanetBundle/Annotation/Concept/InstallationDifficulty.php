<?php
namespace PlanetBundle\Annotation\Concept;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation property contains settings of installation part into Thing
 * @Target({"PROPERTY"})
 */
class InstallationDifficulty
{
    /** @var float */
    private $workHours = 0;
    /** @var int how much agent can do it at same time */
    private $parallelismLimit = null;
    /** @var float 1h/1m3 */
    private $workHourPerCubicMeter = 0;
    /** @var float 1h/1kg */
    private $workHourPerKilo = 0;
    /** @var float 1h/1000kg */
    private $workHourPerTon = 0;

    /**
     * InstallationDifficulty constructor.
     * @param float $workHours
     */
    public function __construct($workHours)
    {
        $this->workHours = $workHours;
    }

    /**
     * @return float
     */
    public function getWorkHours()
    {
        return $this->workHours;
    }

    /**
     * @return int
     */
    public function getParallelismLimit()
    {
        return $this->parallelismLimit;
    }

    /**
     * @return float
     */
    public function getWorkHourPerCubicMeter()
    {
        return $this->workHourPerCubicMeter;
    }

    /**
     * @return float
     */
    public function getWorkHourPerKilo()
    {
        return $this->workHourPerKilo + $this->workHourPerTon / 1000;
    }
}