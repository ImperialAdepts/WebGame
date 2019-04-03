<?php

namespace AppBundle\Controller;

use AppBundle\Builder\Maintainer;
use AppBundle\Builder\PlanetBuilder;
use AppBundle\Descriptor\Adapters\Team;
use AppBundle\Descriptor\Adapters\Workable;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="cron")
 */
class CronController extends Controller
{
	const PEOPLE_BASE_FERTILITY_RATE = 2;

	/**
	 * @Route("/build-projects", name="cron_build_projects")
	 */
	public function buildAction(Request $request)
	{

        $projects = $this->getDoctrine()->getRepository(Entity\Planet\CurrentBuildingProject::class)->getActiveSortedByPriority();
        $builder = $this->get('planet_builder');
        /** @var Entity\Planet\BuildingProject $project */
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
//		/** @var Entity\Planet\CurrentBuildingProject $projectA */
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
		$settlements = $this->getDoctrine()->getRepository(Entity\Planet\Settlement::class)->getAll();

        /** @var Entity\Planet\Settlement $settlement */
        foreach ($settlements as $settlement) {
            /** @var Entity\Planet\Region $region */
            foreach ($settlement->getRegions() as $region) {
                /** @var Team $team */
                foreach (Team::in($region) as $team) {
                    $team->getDeposit()->setWorkHours(24*365);
                    $this->getDoctrine()->getManager()->persist($team->getDeposit());
                }

                $this->get('maintainer_food')->eatFood($region);

                $this->getDoctrine()->getManager()->flush($region);
            }
        }
        $this->get('maintainer')->clearEmptyDeposits();
		$this->getDoctrine()->getManager()->flush();

		$response = "";

		return new Response($response);
	}

	/**
	 * @Route("/births", name="cron_births")
	 */
	public function birthsAction()
	{
		$settlements = $this->getDoctrine()->getRepository(Entity\Planet\Settlement::class)->getAll();

        /** @var Entity\Planet\Settlement $settlement */
        foreach ($settlements as $settlement) {
            /** @var Entity\Planet\Region $region */
            foreach ($settlement->getRegions() as $region) {
                $peopleDeposit = $region->getResourceDeposit(ResourceDescriptorEnum::PEOPLE);
                if (!$peopleDeposit) continue;

                $newborn = round($peopleDeposit->getAmount() * self::PEOPLE_BASE_FERTILITY_RATE / 20) + 1;
                $newPeopleCount = $peopleDeposit->getAmount() + $newborn;
                $peopleDeposit->setAmount($newPeopleCount);

                $this->getDoctrine()->getManager()->persist($peopleDeposit);
            }
		}
		$this->getDoctrine()->getManager()->flush();

		$response = "";

		return new Response($response);
	}
}
