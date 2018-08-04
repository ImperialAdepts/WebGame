<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity;

/**
 * @Route(path="gamer")
 */
class GamerController extends Controller
{
	/**
	 * @Route("/login/{username}", name="gamer_login")
	 */
	public function loginAction($username, Request $request)
	{
		$this->get('session')->set('current-gamer', $username);
		$this->redirectToRoute('gamer_dashboard');
	}

	/**
	 * @Route("/", name="gamer_dashboard")
	 */
	public function dashboardAction(Request $request)
	{
		$gamer = [];
		$souls = $this->getDoctrine()
			->getRepository(Entity\Soul::class)
			->findAllOrderedByName();
		return $this->render('Gamer/dashboard.html.twig', [
			'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
			'gamer' => $gamer,
			'souls' => $souls,
		]);
	}
}
