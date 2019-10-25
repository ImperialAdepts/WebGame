<?php

namespace AppBundle\Controller;

use AppBundle\Builder\GalaxyBuilder;
use AppBundle\Builder\SpaceSectorAddress;
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
 * @Route(path="solar-system")
 */
class SolarSystemController extends Controller
{

	/**
	 * @Route("/planet-{planet}", name="solar_system_planet_detail")
	 */
	public function planetDetailAction(Entity\SolarSystem\Planet $planet)
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

    /**
     * @Route("/system/{system}", name="solar_system_detail")
     */
    public function systemDetailAction(Entity\SolarSystem\System $system)
    {
        return $this->render('Planet/solar-system.html.twig', [
            'systemAddress' => $system->getSectorAddress(),
            'system' => $system,
            'sun' => $system->getCentralSun(),
        ]);
    }

    /**
     * @Route("/system-guess/{systemAddress}/{localGroupCoord}", name="solar_system_guess")
     */
    public function systemGuessAction($systemAddress, $localGroupCoord)
    {
        /** @var Entity\Galaxy\SectorAddress $spaceAddress */
        $spaceAddress = Entity\Galaxy\SectorAddress::decode($systemAddress);
        $localGroupCoordinations = Entity\Galaxy\SpaceCoordination::decode($localGroupCoord);

        $sector = GalaxyBuilder::getSector($spaceAddress);
        $system = Entity\Galaxy\LocalGroup::buildSystem($sector->getLocalGroup(), $localGroupCoordinations);

        return $this->render('Planet/solar-system.html.twig', [
            'systemAddress' => $systemAddress,
            'system' => $system,
            'sun' => $system->getCentralSun(),
        ]);
    }

}
