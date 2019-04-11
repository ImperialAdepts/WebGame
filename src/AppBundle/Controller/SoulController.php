<?php

namespace AppBundle\Controller;

use AppBundle\EnumAlignmentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity; use PlanetBundle\Entity as PlanetEntity;

/**
 * @Route(path="soul")
 */
class SoulController extends Controller
{
	/**
	 * @Route("/create/{alignment}", name="soul_create_new")
	 */
	public function createNewAction($alignment, Request $request)
	{
	    $soul = new Entity\Soul();
	    $soul->setGamer($this->get('logged_user_settings')->getGamer());
	    $soul->setAlignment($alignment);
	    $soul->setName('Generic name');
	    $this->getDoctrine()->getManager()->persist($soul);
	    $this->getDoctrine()->getManager()->flush();
		return $this->forward('AppBundle:Gamer:dashboard');
	}

}
