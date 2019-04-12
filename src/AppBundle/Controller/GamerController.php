<?php

namespace AppBundle\Controller;

use AppBundle\EnumAlignmentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity;
use PlanetBundle\Entity as PlanetEntity;

/**
 */
class GamerController extends Controller
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
	 * @Route("/human-selection", name="gamer_human_selection")
	 */
	public function dashboardAction()
	{
		$gamer = $this->get('logged_user_settings')->getGamer();
		$soulRepository = $this->getDoctrine()->getRepository(Entity\Soul::class);
		return $this->render('Gamer/dashboard.html.twig', [
			'gamer' => $gamer,
			EnumAlignmentType::CHAOTIC_NEUTRAL.'_souls' => $soulRepository->getByGamerAlignment($gamer, EnumAlignmentType::CHAOTIC_NEUTRAL),
			EnumAlignmentType::CHAOTIC_GOOD.'_souls' => $soulRepository->getByGamerAlignment($gamer, EnumAlignmentType::CHAOTIC_GOOD),
			EnumAlignmentType::CHAOTIC_EVIL.'_souls' => $soulRepository->getByGamerAlignment($gamer, EnumAlignmentType::CHAOTIC_EVIL),
			EnumAlignmentType::NEUTRAL_NEUTRAL.'_souls' => $soulRepository->getByGamerAlignment($gamer, EnumAlignmentType::NEUTRAL_NEUTRAL),
			EnumAlignmentType::NEUTRAL_GOOD.'_souls' => $soulRepository->getByGamerAlignment($gamer, EnumAlignmentType::NEUTRAL_GOOD),
			EnumAlignmentType::NEUTRAL_EVIL.'_souls' => $soulRepository->getByGamerAlignment($gamer, EnumAlignmentType::NEUTRAL_EVIL),
			EnumAlignmentType::LAWFUL_GOOD.'_souls' => $soulRepository->getByGamerAlignment($gamer, EnumAlignmentType::LAWFUL_GOOD),
			EnumAlignmentType::LAWFUL_NEUTRAL.'_souls' => $soulRepository->getByGamerAlignment($gamer, EnumAlignmentType::LAWFUL_NEUTRAL),
			EnumAlignmentType::LAWFUL_EVIL.'_souls' => $soulRepository->getByGamerAlignment($gamer, EnumAlignmentType::LAWFUL_EVIL),
		]);
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
