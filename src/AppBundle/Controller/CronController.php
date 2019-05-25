<?php

namespace AppBundle\Controller;

use AppBundle\Builder\Maintainer;
use AppBundle\Builder\PlanetBuilder;
use AppBundle\Descriptor\Adapters\Team;
use AppBundle\Descriptor\Adapters\Workable;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Entity; use PlanetBundle\Entity as PlanetEntity;
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
     * @Route("/time-jump", name="cron_time_jump")
     */
    public function timeJumpAction()
    {
        /** @var Entity\SolarSystem\Planet[] $planets */
        $planets = $this->getDoctrine()->getManager()->getRepository(Entity\SolarSystem\Planet::class)->getUnrefreshed();

        $response = "<table>";

        foreach ($planets as $planet) {
            $response .= "<tr><th>Planet name: {$planet->getName()} [{$planet->getType()}]</th></tr>";
            $response .= "<tr><th>Phase updated: {$planet->getLastPhaseUpdate()} [".date('d. m. Y H:i', $planet->getNextUpdateTime())."]</th></tr>";

            $response .= "<tr><td>epoche:</td>".$this->printPhaseInfo($planet, 0);
            $response .= "</tr>";
            $response .= "<tr><td>now:</td>".$this->printPhaseInfo($planet, time());
            $response .= "</tr>";
            $response .= "<tr><td>in hour:</td>".$this->printPhaseInfo($planet, time()+60*60);
            $response .= "</tr>";;
            $response .= "<tr><td>tomorow:</td>".$this->printPhaseInfo($planet, time()+24*60*60);
            $response .= "</tr>";
            $response .= "<tr><td>week:</td>".$this->printPhaseInfo($planet, time()+7*24*60*60);
            $response .= "</tr>";
        }
        $response .= "</table>";


        foreach ($planets as $planet) {
            if ($planet->getLastPhaseUpdate() === null) {
                $planet->setLastPhaseUpdate(TimeTransformator::timestampToPhase($planet, time()));
            } else {
                $planet->setLastPhaseUpdate($planet->getLastPhaseUpdate()+1);
            }
            $this->update($planet);
            $planet->setNextUpdateTime(TimeTransformator::phaseToTimestamp($planet, $planet->getLastPhaseUpdate()+1));

            $this->getDoctrine()->getManager()->persist($planet);
        }
        $this->getDoctrine()->getManager()->flush();

        return new Response($response);
    }

    public function printPhaseInfo(Entity\SolarSystem\Planet $planet, $currentTimestamp) {
        $currentPhase = TimeTransformator::timestampToPhase($planet, $currentTimestamp);
        $info = "<td>";
        $info .= "#".$currentPhase;
        $info .= "</td><td>";
        $info .= "[";
        $info .= date('d. m. Y H:i', TimeTransformator::phaseToTimestamp($planet, $currentPhase));
        $info .= "</td><td> - ";
        $info .= "</td><td>";
        $info .= date('d. m. Y H:i', TimeTransformator::phaseToTimestamp($planet, $currentPhase+1));
        $info .= "]";
        $info .= "</td>";
        $info .= "<td>".($planet->getOrbitPhaseLengthInSec()/(3600*$planet->getTimeCoefficient()))."h</td>";
        return $info;
    }

	/**
	 * @Route("/build-projects", name="cron_build_projects")
	 */
	public function buildAction(Request $request)
	{

        $projects = $this->getDoctrine()->getRepository(PlanetEntity\CurrentBuildingProject::class)->getActiveSortedByPriority();
        $builder = $this->get('planet_builder');
        /** @var PlanetEntity\BuildingProject $project */
        foreach ($projects as $project) {
            $builder->buildProjectStep($project);
            if ($project->isDone()) {
                $builder->buildProject($project);
                $this->getDoctrine()->getManager()->remove($project);
            } else {
                $this->getDoctrine()->getManager()->persist($project);
            }
        }
        $this->getDoctrine()->getManager()->flush();

		$response = "";
//		echo "Count=".count($projects)."<br>\n";
//		/** @var PlanetEntity\CurrentBuildingProject $projectA */
//        foreach ($projects as $projectA) {
//            echo "{$projectA->getRegion()->getCoords()} :";
//            echo "{$projectA->getBuildingBlueprint()->getDescription()}<br>\n";
//			$response .= "PROJECT at " . $projectA->getRegion()->getCoords() . ' ' . $projectA->getBuildingBlueprint()->getDescription() . "\n<br>";
//			if (!$projectA->getNotifications()) continue;
//			foreach ($projectA->getNotifications() as $notification) {
//				$response .= "{$notification->getDescription()}\n<br>";
//			}
//		}

		return new Response($response);
	}

	/**
	 * @Route("/maintanance", name="cron_maintanance")
	 */
	public function maintananceAction()
	{
	    // TODO: presunout do zpracovani jednotlivych planet
//		$settlements = $this->getDoctrine()->getRepository(PlanetEntity\Settlement::class)->getAll();
//
//        /** @var PlanetEntity\Settlement $settlement */
//        foreach ($settlements as $settlement) {
//            /** @var PlanetEntity\Region $region */
//            foreach ($settlement->getRegions() as $region) {
//                /** @var Team $team */
//                foreach (Team::in($region) as $team) {
//                    $team->getDeposit()->setWorkHours(24*365);
//                    $this->getDoctrine()->getManager()->persist($team->getDeposit());
//                }
//
//                $this->get('maintainer_food')->eatFood($region);
//
//                $this->getDoctrine()->getManager()->flush($region);
//            }
//        }

//        $this->get('maintainer')->clearEmptyDeposits();
        $this->get('maintainer_human')->addHumanHours();
        $this->get('maintainer_human')->resetFeelings();
		$this->getDoctrine()->getManager()->flush();

		$response = "";

		return new Response($response);
	}

	/**
	 * @Route("/births", name="cron_births")
	 */
	public function birthsAction()
	{
		$settlements = $this->getDoctrine()->getRepository(PlanetEntity\Settlement::class)->getAll();

        /** @var PlanetEntity\Settlement $settlement */
        foreach ($settlements as $settlement) {
            /** @var PlanetEntity\Region $region */
            foreach ($settlement->getRegions() as $region) {
                $this->get('maintainer_population')->doBirths($region);

                $this->getDoctrine()->getManager()->persist($region);
            }
		}
		$this->getDoctrine()->getManager()->flush();

		$response = "";

		return new Response($response);
	}

    private function update(Entity\SolarSystem\Planet $planet)
    {
        $this->get('dynamic_planet_connector')->setPlanet($planet);
        Debugger::dump($planet);

        $settlements = $this->getDoctrine()->getManager('planet')->getRepository(PlanetEntity\Settlement::class)->findAll();
        Debugger::dump($settlements);
    }
}
