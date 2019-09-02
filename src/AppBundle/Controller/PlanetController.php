<?php

namespace AppBundle\Controller;

use AppBundle\Descriptor\TimeTransformator;
use AppBundle\Entity\Human\EventTypeEnum;
use AppBundle\EnumAlignmentType;
use PlanetBundle\Maintainer\LifeMaintainer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity;
use PlanetBundle\Entity as PlanetEntity;

/**
 * @Route(path="planet")
 */
class PlanetController extends Controller
{

	/**
	 * @Route("/{planet}", name="planet_dashboard")
	 */
	public function dashboardAction(Entity\SolarSystem\Planet $planet)
	{
        $peopleCount = 666;

        $phaseLengthInHours = TimeTransformator::phaseToTimestamp($planet, $planet->getLastPhaseUpdate()+1) - TimeTransformator::phaseToTimestamp($planet, $planet->getLastPhaseUpdate());
        $phaseLengthInHours = round($phaseLengthInHours/3600, 1);

		return $this->render('Planet/dashboard.html.twig', [
            'planet' => $planet,
            'peopleCount' => $peopleCount,
            'phaseLength' => $phaseLengthInHours,
            'endphase' => TimeTransformator::phaseToTimestamp($planet, $planet->getLastPhaseUpdate()+1),
		]);
	}

}
