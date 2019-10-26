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
            'rightSectors' => [],
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

        $sectors = [];
        $firstInLine = $currentSector->getAddress();
        for($i = 0; $i< 5; $i++) {
            $line = [];
            $line[] = $firstInLine;
            $lastInLine = $firstInLine;
            for($x = 0; $x< 5; $x++) {
                $line[] = $lastInLine = $lastInLine->getUp();
            }
            $firstInLine = $firstInLine->getRight();
            $sectors[] = $line;
        }

        return $this->render('Galaxy/map.html.twig', [
            'currentSector' => $currentSector,
            'sectors' => $sectors,
        ]);
    }
}
