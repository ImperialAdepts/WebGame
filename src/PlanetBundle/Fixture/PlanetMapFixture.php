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
    const PLANET_HEMISPHERE_SIZE = 10;

	/**
	 * Load data fixtures with the passed EntityManager
	 *
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(\Doctrine\Common\Persistence\ObjectManager $manager)
	{
	    $index = 0;
        foreach ($this->getRegions() as $regionPeaks) {
            $centralPeak = $manager->getRepository(PlanetEntity\Peak::class)->findOrCreateByCoords(
                $regionPeaks['center']['w'],
                $regionPeaks['center']['h'],
                $regionPeaks['center']['h']+abs($regionPeaks['center']['w']*$regionPeaks['center']['h']) % 30
            );
            $leftPeak = $manager->getRepository(PlanetEntity\Peak::class)->findOrCreateByCoords(
                $regionPeaks['left']['w'],
                $regionPeaks['left']['h'],
                $regionPeaks['left']['h']+abs($regionPeaks['left']['w']*$regionPeaks['left']['h']) % 30
            );
            $rightPeak = $manager->getRepository(PlanetEntity\Peak::class)->findOrCreateByCoords(
                $regionPeaks['right']['w'],
                $regionPeaks['right']['h'],
                $regionPeaks['right']['h']+abs($regionPeaks['right']['w']*$regionPeaks['right']['h']) % 30
            );

            $region = new PlanetEntity\Region($centralPeak, $leftPeak, $rightPeak);
            $region->setFertility(abs(($centralPeak->getXcoord()*$leftPeak->getYcoord() % 6)) * 10);
            $manager->persist($region);
            $manager->persist($centralPeak);
            $manager->persist($leftPeak);
            $manager->persist($rightPeak);
            $manager->flush();
            if ((++$index % 100) == 0) {
                echo "region count generated: $index\n";
            }
        }

		$manager->flush();
	}

    private function getPeaks() {
        foreach (range(0, self::PLANET_HEMISPHERE_SIZE-1) as $height) {
            foreach (range(0, $this->getWidthLength($height)-1) as $width) {
                yield ['w' => $width, 'h' => $height];
            }
        }
        yield ['w' => 0, 'h' => self::PLANET_HEMISPHERE_SIZE];
        foreach (range(-1, -(self::PLANET_HEMISPHERE_SIZE-1), -1) as $height) {
            foreach (range(0, $this->getWidthLength($height)-1) as $width) {
                yield ['w' => $width, 'h' => $height];
            }
        }
        yield ['w' => 0, 'h' => -self::PLANET_HEMISPHERE_SIZE];
    }

    /**
     * @param $height
     * @return int
     */
    private function getWidthLength($height) {
        if (abs($height) == self::PLANET_HEMISPHERE_SIZE) {
            return 1;
        }
        return 4*(self::PLANET_HEMISPHERE_SIZE-abs($height));
    }

	private function getRegions() {
        foreach ($this->getPeaks() as $pcoord) {
            $HLen = $this->getWidthLength($pcoord['h']);
            if (abs($pcoord['h']) === self::PLANET_HEMISPHERE_SIZE) {
                continue;
            }

            $left = $pcoord;
            $right = ['w' => ($pcoord['w']+1) % $HLen, 'h' => $pcoord['h']];

            $centerWidthDiff = floor(4*$pcoord['w']/$HLen);

            if ($pcoord['h'] > 0) {
                $highCenter = ['w' => ($pcoord['w'] - $centerWidthDiff) % $this->getWidthLength($pcoord['h']+1), 'h' => $pcoord['h']+1];
                $bottomCenter = ['w' => ($pcoord['w'] + $centerWidthDiff + 1) % $this->getWidthLength($pcoord['h']-1), 'h' => $pcoord['h']-1];
            } elseif($pcoord['h'] == 0) {
                $highCenter = ['w' => ($pcoord['w'] - $centerWidthDiff) % $this->getWidthLength($pcoord['h']+1), 'h' => $pcoord['h']+1];
                $bottomCenter = ['w' => ($pcoord['w'] - $centerWidthDiff) % $this->getWidthLength($pcoord['h']-1), 'h' => $pcoord['h']-1];
            } else {
                $highCenter = ['w' => ($pcoord['w'] + $centerWidthDiff + 1) % $this->getWidthLength($pcoord['h']+1), 'h' => $pcoord['h']+1];
                $bottomCenter = ['w' => ($pcoord['w'] - $centerWidthDiff) % $this->getWidthLength($pcoord['h']-1), 'h' => $pcoord['h']-1];
            }
            yield ['left' => $left, 'right' => $right, 'center' => $highCenter];
            yield ['left' => $left, 'right' => $right, 'center' => $bottomCenter];
        }
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