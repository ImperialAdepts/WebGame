<?php
namespace PlanetBundle\Annotation\Concept;


/**
 * @Annotation property contains settings of installation part into Thing
 * @Target({"PROPERTY"})
 */
class CreationDifficulty
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
}