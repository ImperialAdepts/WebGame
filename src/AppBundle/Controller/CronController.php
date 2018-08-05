<?php

namespace AppBundle\Controller;

use AppBundle\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="cron")
 */
class CronController extends Controller
{

	/**
	 * @Route("/build-projects", name="cron_build_projects")
	 */
	public function buildAction(Request $request)
	{
		$projects = $this->getDoctrine()->getRepository(Entity\Planet\BuildingProject::class)->getAllSortedByPriority();

		/** @var Entity\Planet\BuildingProject $project */
		foreach ($projects as $project) {
			echo $project->getRegion()->getUuid().' '.$project->getBuilding().' Left '.$project->getMandaysLeft()."<br>\n";
			$project->setMandaysLeft($project->getMandaysLeft()-1);
			if ($project->isDone()) {
				$settlement = new Entity\Planet\Settlement();
				$settlement->setType($project->getBuilding());
				$settlement->setRegions([$project->getRegion()]);
				$settlement->setOwner($project->getSupervisor());
				$settlement->setManager($project->getSupervisor());
				$project->getRegion()->setSettlement($settlement);
				$this->getDoctrine()->getManager()->persist($settlement);
				$this->getDoctrine()->getManager()->persist($project->getRegion());
				$this->getDoctrine()->getManager()->remove($project);
			} else {
				$this->getDoctrine()->getManager()->persist($project);
			}
		}
		$this->getDoctrine()->getManager()->flush();

		return new \Symfony\Component\HttpFoundation\Response("OK");
	}

}
