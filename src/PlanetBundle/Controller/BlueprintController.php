<?php

namespace PlanetBundle\Controller;

use PlanetBundle\Concept\Concept;
use PlanetBundle\Concept\ConceptToBlueprintAdapter;
use PlanetBundle\Form\BlueprintAdapter;
use PlanetBundle\Form\BlueprintFormType;
use PlanetBundle\Repository\ConceptRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
            /** @var BlueprintAdapter $blueprintAdapter */
            $blueprintAdapter = $form->getData();
            $blueprint = new PlanetEntity\Resource\Blueprint();
            $blueprint->setConcept($conceptName);
            $blueprintAdapter->setIntoEntity($blueprint);

            $this->get('doctrine.orm.planet_entity_manager')->persist($blueprint);
            $this->get('doctrine.orm.planet_entity_manager')->flush();

            $this->addFlash('done', "Blueprint {$blueprint->getDescription()} created");
            return $this->redirectToRoute('blueprint_edit', ['blueprint' => $blueprint->getId()]);
        }

        $conceptNames = explode('\\', $conceptName);
        $conceptLastName = array_pop($conceptNames);

        return $this->render('Blueprint/create.html.twig', [
            'concept' => $conceptLastName,
            'createForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{blueprint}", name="blueprint_edit")
     */
    public function editAction(PlanetEntity\Resource\Blueprint $blueprint, Request $request)
    {
        return $this->render('Blueprint/edit.html.twig', [
            'blueprint' => $blueprint,
        ]);
    }

    /**
     * @Route("/edit/{blueprint}", name="blueprint_edit_fragment", methods={"GET"})
     */
    public function editFragmentAction(PlanetEntity\Resource\Blueprint $blueprint, Request $request)
    {
        $blueprintTraitForm = $this->createForm(BlueprintFormType::class, new BlueprintAdapter($blueprint), [
            'concept' => $blueprint->getConcept(),
            'action' => $this->generateUrl('blueprint_edit_fragment_save'),
        ]);

        $availableBlueprints = [];
        foreach (ConceptToBlueprintAdapter::getParts($blueprint->getConcept()) as $partName => $useCase) {
            $availableBlueprints[$partName] = $this->get('repo_blueprint')->getByUseCase($useCase);
        }

        $testPlanet = new GlobalEntity\SolarSystem\Planet();
        $testPlanet->setOrbitPeriod(2000);

        // TODO: kontext naplnit informacemi od postavy hrace
        /** @var Concept $concept */
        $concept = $blueprint->getConceptAdapter();
        $concept->addContext('planet', $testPlanet);

        $statistics = [];
        /**
         * @var string $description
         * @var \ReflectionMethod $method
         */
        foreach (ConceptToBlueprintAdapter::getDependentInformations($blueprint->getConcept()) as $description => $method) {
            $args = [];
            foreach ($method->getParameters() as $parameter) {
                $args[] = $concept->getContext($parameter->getName());
            }
            $statistics[$description] = $method->invokeArgs($concept, $args);
        }

        return $this->render('Blueprint/edit-fragment.html.twig', [
            'blueprint' => $blueprint,
            'traitForm' => $blueprintTraitForm->createView(),
            'parts' => ConceptToBlueprintAdapter::getParts($blueprint->getConcept()),
            'statistics' => $statistics,
            'availableBlueprints' => $availableBlueprints,
        ]);
    }

    /**
     * @Route("/save", name="blueprint_edit_fragment_save", methods={"POST"})
     */
    public function editFragmentSaveAction(Request $request)
    {
        $blueprintFormData = $request->get('blueprint_form');
        /** @var PlanetEntity\Resource\Blueprint $blueprint */
        $blueprint = $this->get('repo_blueprint')->find($blueprintFormData['blueprintId']);
        $blueprintTraitForm = $this->createForm(BlueprintFormType::class, new BlueprintAdapter($blueprint), ['concept' => $blueprint->getConcept()]);

        $blueprintTraitForm->handleRequest($request);

        if ($blueprintTraitForm->isSubmitted() && $blueprintTraitForm->isValid()) {
            /** @var BlueprintAdapter $blueprintAdapter */
            $blueprintAdapter = $blueprintTraitForm->getData();
            $blueprintAdapter->setIntoEntity($blueprint);

            $this->get('doctrine.orm.planet_entity_manager')->persist($blueprint);
            $this->get('doctrine.orm.planet_entity_manager')->flush();

            $this->addFlash('done', "Blueprint {$blueprintAdapter->getName()} created");

            return $this->redirectToRoute('blueprint_edit_fragment', [
                'blueprint' => $blueprint->getId(),
            ]);
        }
    }

    /**
     * @Route("/set-{blueprintTo}-into-{part}-{blueprint}", name="blueprint_set_part")
     */
    public function setBlueprintToPartAction(PlanetEntity\Resource\Blueprint $blueprintTo, PlanetEntity\Resource\Blueprint $blueprint, $part, Request $request)
    {
        $blueprintTo->addPart($part, $blueprint);

        $this->get('doctrine.orm.planet_entity_manager')->persist($blueprintTo);
        $this->get('doctrine.orm.planet_entity_manager')->flush();

        return $this->redirectToRoute('blueprint_edit_fragment', [
            'blueprint' => $blueprintTo->getId(),
        ]);
    }
}
