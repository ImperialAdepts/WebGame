<?php

namespace AppBundle\Controller;

use AppBundle\Builder\GalaxyBuilder;
use AppBundle\Builder\SpaceSector;
use AppBundle\Builder\SpaceSectorAddress;
use AppBundle\Builder\SpaceSectorCoordination;
use AppBundle\Descriptor\TimeTransformator;
use AppBundle\Entity\Human\EventTypeEnum;
use AppBundle\EnumAlignmentType;
use PlanetBundle\Maintainer\LifeMaintainer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity;
use PlanetBundle\Entity as PlanetEntity;

/**
 * @Route(path="galaxy")
 */
class GalaxyController extends Controller
{

    const RECURSIVE_COUNT = 3;

	/**
	 * @Route("/map", name="galaxy_picture")
	 */
	public function mapAction()
	{
		return $this->render('Galaxy/map.html.twig', [
		    'sectors' => GalaxyBuilder::getFirstLevelSectors(),
		]);
	}

    /**
     * @Route("/sector/{addressCode}", name="galaxy_sector")
     */
    public function sectorAction($addressCode)
    {
        /** @var Entity\Galaxy\SpaceSectorAddress $spaceAddress */
        $spaceAddress = Entity\Galaxy\SpaceSectorAddress::decode($addressCode);

        if ($spaceAddress->getSize() < self::RECURSIVE_COUNT) {
            $sector = GalaxyBuilder::getSector($spaceAddress);
            $sectors = [];

            for ($x = 0; $x < GalaxyBuilder::SECTOR_FACTOR; $x++) {
                for ($y = 0; $y < GalaxyBuilder::SECTOR_FACTOR; $y++) {
                    $sectors[$x][$y] = $sector->getSubSector(new Entity\Galaxy\SpaceSectorCoordination($x, $y, 0));
                }
            }

            return $this->render('Galaxy/map.html.twig', [
                'sectors' => $sectors,
            ]);
        } else {
            $systems = [];
            for ($i = 0; $i < rand(2, 5); $i++) {
                $systems[] = GalaxyBuilder::buildSystem($spaceAddress->getSubAddress(new Entity\Galaxy\SpaceSectorCoordination($i, 0, 0)));
            }
            return $this->render('Galaxy/sector-detail.html.twig', [
                'systems' => $systems,
            ]);
        }
    }

}
