<?php

namespace PlanetBundle\Builder;

use AppBundle\Entity as GeneralEntity;
use PlanetBundle\Entity as PlanetEntity;
use AppBundle\Entity\SolarSystem\Planet;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PlanetMapBuilder
{
    /**
     * The dependency injection container.
     *
     * @var ContainerInterface
     */
    private $container;
    /** @var int */
    private $hemisphereSize;

    /**
     * PlanetMapGenerator constructor.
     * @param ContainerInterface $container
     * @param int $hemisphereSize
     */
    public function __construct(ContainerInterface $container, $hemisphereSize)
    {
        $this->container = $container;
        $this->hemisphereSize = $hemisphereSize;
    }

    public function build(\Doctrine\Common\Persistence\ObjectManager $planetManager, Planet $planet)
    {
        $index = 0;
        foreach ($this->getRegions() as $regionPeaks) {
            $centralPeak = $planetManager->getRepository(PlanetEntity\Peak::class)->findOrCreateByCoords(
                $regionPeaks['center']['w'],
                $regionPeaks['center']['h'],
                $regionPeaks['center']['h']+abs($regionPeaks['center']['w']*$regionPeaks['center']['h']) % 30
            );
            $leftPeak = $planetManager->getRepository(PlanetEntity\Peak::class)->findOrCreateByCoords(
                $regionPeaks['left']['w'],
                $regionPeaks['left']['h'],
                $regionPeaks['left']['h']+abs($regionPeaks['left']['w']*$regionPeaks['left']['h']) % 30
            );
            $rightPeak = $planetManager->getRepository(PlanetEntity\Peak::class)->findOrCreateByCoords(
                $regionPeaks['right']['w'],
                $regionPeaks['right']['h'],
                $regionPeaks['right']['h']+abs($regionPeaks['right']['w']*$regionPeaks['right']['h']) % 30
            );

            $region = new PlanetEntity\Region($centralPeak, $leftPeak, $rightPeak);
            $region->setFertility(abs(($centralPeak->getXcoord()*$leftPeak->getYcoord() % 6)) * 10);
            $planetManager->persist($region);
            $planetManager->persist($centralPeak);
            $planetManager->persist($leftPeak);
            $planetManager->persist($rightPeak);
            $planetManager->flush();
            if ((++$index % 100) == 0) {
                echo "Planet {$planet->getName()}: region count generated: $index\n";
            }
        }

        $planetManager->flush();
    }

    private function getPeaks() {
        foreach (range(0, $this->hemisphereSize-1) as $height) {
            foreach (range(0, $this->getWidthLength($height)-1) as $width) {
                yield ['w' => $width, 'h' => $height];
            }
        }
        yield ['w' => 0, 'h' => $this->hemisphereSize];
        foreach (range(-1, -($this->hemisphereSize-1), -1) as $height) {
            foreach (range(0, $this->getWidthLength($height)-1) as $width) {
                yield ['w' => $width, 'h' => $height];
            }
        }
        yield ['w' => 0, 'h' => -$this->hemisphereSize];
    }

    /**
     * @param $height
     * @return int
     */
    private function getWidthLength($height) {
        if (abs($height) == $this->hemisphereSize) {
            return 1;
        }
        return 4*($this->hemisphereSize-abs($height));
    }

    private function getRegions() {
        foreach ($this->getPeaks() as $pcoord) {
            $HLen = $this->getWidthLength($pcoord['h']);
            if (abs($pcoord['h']) === $this->hemisphereSize) {
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
}