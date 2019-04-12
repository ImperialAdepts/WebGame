<?php
namespace PlanetBundle\Fixture;

use AppBundle\Entity as GeneralEntity;
use AppBundle\Fixture\PlanetsFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use PlanetBundle\Entity as PlanetEntity;

/**
 * vygeneruje malou testovaci mapu
 */
class PlanetMapFixture extends \Doctrine\Bundle\FixturesBundle\Fixture implements DependentFixtureInterface
{

	/**
	 * Load data fixtures with the passed EntityManager
	 *
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(\Doctrine\Common\Persistence\ObjectManager $manager)
	{
		$pcoords = [
		    1 => [0,0],
		    2 => [0,1],
		    3 => [0,2],
		    4 => [1,0],
		    5 => [1,1],
		    6 => [1,2],
		    7 => [1,3],
		    8 => [2,0],
		    9 => [2,1],
		    10 => [2,2],
		    11 => [2,3],
		    12 => [2,4],
		    13 => [3,0],
		    14 => [3,1],
		    15 => [3,2],
		    16 => [3,3],
		    17 => [3,4],
		    18 => [3,5],
        ];
		$p = [];
		foreach ($pcoords as $id => $peakIndex) {
		    $peak = new PlanetEntity\Peak($id);
		    $peak->setXcoord($peakIndex[0]);
		    $peak->setYcoord($peakIndex[1]);
		    $p[$id] = $peak;
		    $manager->persist($peak);
        }
		$regions = [
		    [1,4,5],
		    [5,1,2],
		    [2,5,6],
		    [6,2,3],
		    [3,6,7],
		    [4,8,9],
		    [9,4,5],
		    [5,9,10],
		    [10,5,6],
		    [6,10,11],
		    [11,6,7],
		    [7,11,2],
		    [8,13,14],
		    [14,8,9],
		    [9,14,15],
		    [15,9,10],
		    [10,15,16],
		    [16,10,11],
		    [11,16,17],
		    [17,11,12],
		    [12,17,18],
        ];
		foreach ($regions as $regionPeaks) {
		    /** @var Peak $centralPeak */
		    $centralPeak = $p[$regionPeaks[0]];
		    $leftPeak = $p[$regionPeaks[1]];
		    $rightPeak = $p[$regionPeaks[2]];
            $region = new PlanetEntity\Region($centralPeak, $leftPeak, $rightPeak);
            $region->setFertility(($centralPeak->getId() % 4) * 10);
            $manager->persist($region);
        }

		$manager->flush();
	}

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            PlanetsFixture::class,
        ];
    }
}