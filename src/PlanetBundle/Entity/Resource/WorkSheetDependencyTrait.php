<?php

namespace PlanetBundle\Entity\Resource;

use Doctrine\ORM\Mapping as ORM;

trait WorkSheetDependencyTrait
{
    /**
     * @var WorkSheet
     *
     * @ORM\ManyToOne(targetEntity="PlanetBundle\Entity\Resource\WorkSheet")
     * @ORM\JoinColumn(name="work_sheet_id", referencedColumnName="id", nullable=false)
     */
    private $workSheet;

    /**
     * @return WorkSheet
     */
    public function getWorkSheet()
    {
        return $this->workSheet;
    }

    /**
     * @param WorkSheet $workSheet
     */
    public function setWorkSheet(WorkSheet $workSheet)
    {
        $this->workSheet = $workSheet;
    }

}

