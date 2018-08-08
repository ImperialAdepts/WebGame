<?php

namespace AppBundle\Controller;

use AppBundle\Builder\PlanetBuilder;
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
		$builder = new PlanetBuilder($this->getDoctrine()->getManager());
		/** @var Entity\Planet\BuildingProject $project */
		foreach ($projects as $project) {
			echo $project->getRegion()->getUuid().' '.$project->getBuilding().' Left '.$project->getMandaysLeft()."<br>\n";
			$project->setMandaysLeft($project->getMandaysLeft()-1);
			if ($project->isDone()) {
				$builder->buildProject($project);
				$this->getDoctrine()->getManager()->remove($project);
			} else {
				$this->getDoctrine()->getManager()->persist($project);
			}
		}
		$this->getDoctrine()->getManager()->flush();

		return new \Symfony\Component\HttpFoundation\Response("OK");
	}

}
