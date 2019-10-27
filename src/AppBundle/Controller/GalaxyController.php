<?php

namespace AppBundle\Controller;

use AppBundle\Builder\GalaxyBuilder;
use AppBundle\Builder\SpaceSector;
use AppBundle\Builder\SpaceSectorAddress;
use AppBundle\Builder\SpaceSectorCoordination;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity;
use PlanetBundle\Entity as PlanetEntity;
use Tracy\Debugger;

/**
 * @Route(path="galaxy")
 */
class GalaxyController extends Controller
{
	/**
	 * @Route("/map", name="galaxy_picture")
	 */
	public function mapAction()
	{
	    $currentSector = GalaxyBuilder::getSector(Entity\Galaxy\SectorAddress::createZeroSectorAddress());

		return $this->render('Galaxy/map.html.twig', [
		    'currentSector' => $currentSector,
            'sectors' => $this->getSectorsAround(Entity\Galaxy\SectorAddress::createZeroSectorAddress()),
		]);
	}

    /**
     * @Route("/map/{addressCode}", name="galaxy_sector")
     */
    public function sectorMapAction($addressCode)
    {
        /** @var Entity\Galaxy\SectorAddress $spaceAddress */
        $spaceAddress = Entity\Galaxy\SectorAddress::decode($addressCode);
        $currentSector = GalaxyBuilder::getSector($spaceAddress);

        return $this->render('Galaxy/map.html.twig', [
            'currentSector' => $currentSector,
            'sectors' => $this->getSectorsAround($spaceAddress),
        ]);
    }

    /**
     * @Route("/map/system/{system}", name="galaxy_system")
     */
    public function systemMapAction(Entity\SolarSystem\System $system)
    {
        $currentSector = GalaxyBuilder::getSector($system->getSectorAddress());

        return $this->render('Galaxy/map.html.twig', [
            'currentSector' => $currentSector,
            'currentSystem' => $system,
            'sectors' => $this->getSectorsAround($system->getSectorAddress()),
        ]);
    }

    private function getSectorsAround(Entity\Galaxy\SectorAddress $address) {
        $currentSector = GalaxyBuilder::getSector($address);

        $sectors = [];
        $firstInLine = $currentSector->getAddress();
        for($i = 0; $i< 5; $i++) {
            $line = [];
            $lastInLine = $firstInLine;
            for($x = 0; $x< 5; $x++) {
                $line[] = GalaxyBuilder::getSector($lastInLine);
                $lastInLine = $lastInLine->getUp();
            }
            $firstInLine = $firstInLine->getRight();
            $sectors[] = $line;
        }

        return $sectors;
    }
}
