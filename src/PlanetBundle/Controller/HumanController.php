<?php

namespace PlanetBundle\Controller;

use AppBundle\Entity\Human\EventDataTypeEnum;
use AppBundle\Entity\Human\EventTypeEnum;
use AppBundle\Fixture\ResourceAndBlueprintFixture;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use PlanetBundle\Entity as PlanetEntity;
use AppBundle\Entity as GlobalEntity;

/**
 * @Route(path="human")
 */
class HumanController extends BasePlanetController
{
	/**
	 * @Route("/", name="human_dashboard")
	 */
	public function dashboardAction(Request $request)
	{
		return $this->redirectToRoute('map_dashboard');
	}
}
