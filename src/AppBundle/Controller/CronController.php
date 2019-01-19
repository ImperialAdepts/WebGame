<?php

namespace AppBundle\Controller;

use AppBundle\Builder\PlanetBuilder;
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
	const PEOPLE_CONSUMPTION = 1.1;
	const PEOPLE_STARVATION_RATIO = 0.3;
	const PEOPLE_BASE_FERTILITY_RATE = 2;

	/**
	 * @Route("/build-projects", name="cron_build_projects")
	 */
	public function buildAction(Request $request)
	{

        $projects = $this->getDoctrine()->getRepository(Entity\Planet\CurrentBuildingProject::class)->getActiveSortedByPriority();
        $builder = new PlanetBuilder($this->getDoctrine()->getManager());
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
			$foodDeposit = $settlement->getResourceDeposit(ResourceDescriptorEnum::SIMPLE_FOOD);
			$peopleDeposit = $settlement->getResourceDeposit(ResourceDescriptorEnum::PEOPLE);
			if (!$peopleDeposit) continue;

			$foodNedded = $peopleDeposit ? $peopleDeposit->getAmount() * self::PEOPLE_CONSUMPTION : 0;
			$foodHave = $foodDeposit ? $foodDeposit->getAmount() : 0;

			if ($foodDeposit) {
				if ($foodHave > $foodNedded) {
					$foodDeposit->setAmount($foodHave - $foodNedded);
				} else {
					$foodDeposit->setAmount(0);
					$this->getDoctrine()->getManager()->remove($foodDeposit);
				}
				$this->getDoctrine()->getManager()->persist($foodDeposit);
			}

			if ($foodHave < $foodNedded) {
				$hungryPeople = ($foodNedded - $foodHave) / self::PEOPLE_CONSUMPTION;
				$diedPeople = round($hungryPeople * self::PEOPLE_STARVATION_RATIO) + 1;
				$newPeopleCount = $peopleDeposit->getAmount() - $diedPeople;
				$peopleDeposit->setAmount($newPeopleCount);
				if ($newPeopleCount < 1) {
					$this->getDoctrine()->getManager()->remove($peopleDeposit);
				}
			}

			$this->getDoctrine()->getManager()->persist($peopleDeposit);
		}
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
			$peopleDeposit = $settlement->getResourceDeposit(ResourceDescriptorEnum::PEOPLE);
			if (!$peopleDeposit) continue;

			$newborn = round($peopleDeposit->getAmount() * self::PEOPLE_BASE_FERTILITY_RATE / 20) + 1;
			$newPeopleCount = $peopleDeposit->getAmount() + $newborn;
			$peopleDeposit->setAmount($newPeopleCount);

			$this->getDoctrine()->getManager()->persist($peopleDeposit);
		}
		$this->getDoctrine()->getManager()->flush();

		$response = "";

		return new Response($response);
	}
}
