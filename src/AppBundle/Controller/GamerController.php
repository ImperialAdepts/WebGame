<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity; use PlanetBundle\Entity as PlanetEntity;

/**
 */
class GamerController extends Controller
{
	/**
	 * @Route("/login/{username}", name="gamer_login")
	 */
	public function loginAction($username, Request $request)
	{
		$this->get('session')->set('current-gamer', $username);
		return $this->forward('AppBundle:Gamer:dashboard');
	}

	/**
	 * @Route("/", name="gamer_dashboard")
	 */
	public function dashboardAction()
	{
		$gamer = [];
		$souls = $this->getDoctrine()
			->getRepository(Entity\Soul::class)
			->findAllOrderedByName();
		return $this->render('Gamer/dashboard.html.twig', [
			'gamer' => $gamer,
			'souls' => $souls,
		]);
	}
}
