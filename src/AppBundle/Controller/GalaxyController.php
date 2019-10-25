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
		return $this->render('Galaxy/map.html.twig', [
		    'currentSector' => GalaxyBuilder::getSector(Entity\Galaxy\SectorAddress::createZeroSectorAddress()),
		]);
	}

    /**
     * @Route("/map/{addressCode}", name="galaxy_sector")
     */
    public function sectorMapAction($addressCode)
    {
        /** @var Entity\Galaxy\SectorAddress $spaceAddress */
        $spaceAddress = Entity\Galaxy\SectorAddress::decode($addressCode);
        $sector = GalaxyBuilder::getSector($spaceAddress);
        return $this->render('Galaxy/map.html.twig', [
            'currentSector' => $sector,
        ]);
    }
}
