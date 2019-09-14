<?php
namespace PlanetBundle\UseCase;

trait EnergyWeapon
{
    use LongRangeWeapon;
    use EnergyConsumer;
    use EnergyDeposit;
}