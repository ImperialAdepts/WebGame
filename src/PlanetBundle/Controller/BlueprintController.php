<?php

namespace PlanetBundle\Controller;

use AppBundle\Entity\Human\EventDataTypeEnum;
use AppBundle\Entity\Human\EventTypeEnum;
use AppBundle\Fixture\ResourceAndBlueprintFixture;
use PlanetBundle\Concept\Battleship;
use PlanetBundle\Concept\ConceptToBlueprintAdapter;
use PlanetBundle\Concept\Reactor;
use PlanetBundle\Form\BlueprintDTO;
use PlanetBundle\Form\BlueprintFormType;
use PlanetBundle\Repository\ConceptRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use PlanetBundle\Entity as PlanetEntity;
use AppBundle\Entity as GlobalEntity;
use Tracy\Debugger;

/**
 * @Route(path="blueprint")
 */
class BlueprintController extends Controller
{
	/**
	 * @Route("/list", name="blueprint_dashboard")
	 */
	public function dashboardAction(Request $request)
	{
	    $conceptRepository = new ConceptRepository();

	    $concepts = [];
        foreach ($conceptRepository->getAll() as $conceptName) {
            $conceptNames = explode('\\', $conceptName);
            $conceptLastName = array_pop($conceptNames);

            $concepts[$conceptName]['label'] = $conceptLastName;
            $concepts[$conceptName]['blueprints'] = $this->get('repo_blueprint')->findBy(['concept' => $conceptName]);
	    }

        return $this->render('Blueprint/list.html.twig', [
            'concepts' => $concepts,
        ]);
	}

    /**
     * @Route("/create/{conceptName}", name="blueprint_create")
     */
    public function createAction($conceptName, Request $request)
    {
        $form = $this->createForm(BlueprintFormType::class, null, ['concept' => $conceptName]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var BlueprintDTO $blueprint */
            $blueprint = $form->getData();

            $this->get('doctrine.orm.planet_entity_manager')->persist($blueprint->getBlueprintEntity());
            $this->get('doctrine.orm.planet_entity_manager')->flush();

            $this->addFlash('done', "Blueprint {$blueprint->getName()} created");
            return $this->redirectToRoute('blueprint_dashboard');
        }

        $conceptNames = explode('\\', $conceptName);
        $conceptLastName = array_pop($conceptNames);

        return $this->render('Blueprint/create.html.twig', [
            'concept' => $conceptLastName,
            'createForm' => $form->createView(),
        ]);
    }
}
