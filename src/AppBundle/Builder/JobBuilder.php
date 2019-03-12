<?php
namespace AppBundle\Builder;


use AppBundle\Descriptor\Adapters\TeamWorker;
use AppBundle\Entity\Blueprint;
use AppBundle\Entity\Job\ProduceJob;
use AppBundle\Entity\Planet\Region;

class JobBuilder
{
    public function produce(Region $region, Blueprint $blueprint, $team, $amount = 1, $repetition = null) {
        $productionJob = new ProduceJob();
        $productionJob->setRegion($region);
        $productionJob->setAmount($amount);
        $productionJob->setRepetition($repetition);
        $productionJob->setBlueprint($blueprint);
    }
}