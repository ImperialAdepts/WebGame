<?php

namespace PlanetBundle;

use Doctrine\DBAL\Types\Type;
use PlanetBundle\Entity\Job\JobTriggerTypeEnum;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PlanetBundle extends Bundle
{
    public function boot()
    {
        parent::boot();
        Type::addType('job_triggertype_enum', JobTriggerTypeEnum::class);
    }

}
