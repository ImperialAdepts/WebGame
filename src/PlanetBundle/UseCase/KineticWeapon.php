<?php
namespace PlanetBundle\UseCase;

trait KineticWeapon
{
    use LongRangeWeapon;
    use EnergyConsumer;
    use EnergyDeposit;
    use AmmunitionDeposit;
}