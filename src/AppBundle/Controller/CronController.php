<?php

namespace AppBundle\Controller;

use AppBundle\Builder\Maintainer;
use AppBundle\Builder\PlanetBuilder;
use AppBundle\Entity; use PlanetBundle\Entity as PlanetEntity;
use PlanetBundle\Maintainer\PlanetMaintainer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Descriptor\TimeTransformator;
use Tracy\Debugger;

/**
 * @Route(path="cron")
 */
class CronController extends Controller
{
    /**
     * @Route("/new-phase/{planet}/{settlementToRedirect}", name="cron_new_phase")
     */
    public function newPhaseAction(Entity\SolarSystem\Planet $planet, PlanetEntity\Settlement $settlementToRedirect)
    {
        $this->get('dynamic_planet_connector')->setPlanet($planet, true);

        $this->get('maintainer_planet')->goToNewPlanetPhase();
        $this->getDoctrine()->getManager()->flush();
        $this->getDoctrine()->getManager('planet')->flush();

        return $this->redirectToRoute('settlement_dashboard', [
            'planet' => $planet->getId(),
            'settlement' => $settlementToRedirect->getId(),
        ]);
    }
}
