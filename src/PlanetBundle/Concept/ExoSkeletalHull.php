<?php
namespace PlanetBundle\Concept;

use PlanetBundle\Annotation\Concept\InstallationDifficulty;
use PlanetBundle\Annotation\Concept\Part;
use PlanetBundle\Annotation\Concept\Persistent;
use PlanetBundle\UseCase;

class ExoSkeletalHull extends Concept
{
    /**
     * @var Skeleton
     * @Part(Skeleton::class)
     * @InstallationDifficulty(workHours=5, parallelismLimit=4)
     */
    private $skeleton;

    /**
     * @return Skeleton
     */
    public function getSkeleton()
    {
        return $this->skeleton;
    }

    /**
     * @param Skeleton $skeleton
     */
    public function setSkeleton($skeleton)
    {
        $this->skeleton = $skeleton;
    }

}