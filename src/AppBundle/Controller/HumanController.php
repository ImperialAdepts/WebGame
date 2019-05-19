<?php

namespace AppBundle\Controller;

use AppBundle\EnumAlignmentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity;
use PlanetBundle\Entity as PlanetEntity;

/**
 * @Route(path="human")
 */
class HumanController extends Controller
{
	/**
	 * @Route("/login/{login}", name="gamer_login")
	 */
	public function loginAction($login, Request $request)
	{
	    $gamer = $this->getDoctrine()->getManager()->getRepository(Entity\Gamer::class)->findByLogin($login);
	    $this->get('logged_user_settings')->setGamer($gamer);
		return $this->redirectToRoute('gamer_human_selection');
	}

	/**
	 * @Route("/journal", name="human_dashboard")
	 */
	public function dashboardAction()
	{
	    if ($this->get('logged_user_settings')->getHuman() === null) {
            return $this->redirectToRoute('gamer_human_selection');
        }
		return $this->render('Human/dashboard.html.twig', [
		    'human' => $this->get('logged_user_settings')->getHuman(),
		]);
	}

    /**
     * @Route("/happy/{change}", name="human_happy")
     */
    public function happyAction($change, Request $request)
    {
        if ($this->get('logged_user_settings')->getHuman() === null) {
            return $this->redirectToRoute('gamer_human_selection');
        }
        $human = $this->get('logged_user_settings')->getHuman();
        $human->getFeelings()->change($change, "handmade change for test by ".$this->get('logged_user_settings')->getGamer()->getLogin(), [
            'human_cause' => $this->get('logged_user_settings')->getHuman(),
        ]);
        $this->getDoctrine()->getManager()->flush($human->getFeelings());
        return $this->redirectToRoute('human_dashboard');
    }

    /**
     * @Route("/sad/{change}", name="human_sad")
     */
    public function sadAction($change, Request $request)
    {
        if ($this->get('logged_user_settings')->getHuman() === null) {
            return $this->redirectToRoute('gamer_human_selection');
        }
        $human = $this->get('logged_user_settings')->getHuman();
        $human->getFeelings()->change(-1*$change, "handmade change for test by ".$this->get('logged_user_settings')->getGamer()->getLogin(), [
            'human_cause' => $this->get('logged_user_settings')->getHuman(),
        ]);
        $this->getDoctrine()->getManager()->flush($human->getFeelings());
        return $this->redirectToRoute('human_dashboard');
    }

    /**
     * @Route("/play-as-{human}", name="gamer_play_as_human")
     */
    public function playAsHumanAction(Entity\Human $human, Request $request)
    {
        $this->get('logged_user_settings')->setHuman($human);
        return $this->redirectToRoute('human_dashboard');
    }
}
