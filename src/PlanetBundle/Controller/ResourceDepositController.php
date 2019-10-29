<?php

namespace PlanetBundle\Controller;

use PlanetBundle\Concept\Concept;
use PlanetBundle\Concept\ConceptToBlueprintAdapter;
use PlanetBundle\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Tracy\Debugger;

/**
 * @Route(path="deposit")
 */
class ResourceDepositController extends BasePlanetController
{
    /**
     * @Route("/detail/{descriptor}", name="settlement_deposit_detail")
     */
    public function depositDetailAction(Entity\Resource\ResourceDescriptor $descriptor, Request $request)
    {
        $adapters = [];
        $statistics = [];
        if ($descriptor instanceof Entity\Resource\Thing) {
            $conceptAdapter = $descriptor->getConceptAdapter();
            foreach ($descriptor->getBlueprint()->getUseCases() as $useCase) {
                $adapters[$useCase] = $conceptAdapter;
            }

            $conceptAdapter->addContext('planet', $this->getPlanet());

            /**
             * @var string $description
             * @var \ReflectionMethod $method
             */
            foreach (ConceptToBlueprintAdapter::getDependentInformations($descriptor->getBlueprint()->getConcept()) as $description => $method) {
                $args = [];
                foreach ($method->getParameters() as $parameter) {
                    $args[] = $conceptAdapter->getContext($parameter->getName());
                }
                $statistics[$description] = $method->invokeArgs($conceptAdapter, $args);
            }

            foreach (ConceptToBlueprintAdapter::getTraits($descriptor->getBlueprint()->getConcept()) as $name => $constraints) {
                $getterName = 'get'.ucfirst($name);
                $statistics[$name] = $conceptAdapter->$getterName();
            }
        }
        //        $consumption = $this->get('maintainer')->getConsumption($descriptor, $descriptor);
        //        $production = $this->get('maintainer')->getProduction($descriptor, $descriptor);
        $consumption = 0;
        $production = 0;
        return $this->render('ResourceDeposit/resource-deposit-fragment.html.twig', [
            'deposit' => $descriptor,
            'consumption' => $consumption,
            'production' => $production,
            'amountChange' => $production - $consumption,
            'adapters' => $adapters,
            'statistics' => $statistics,
        ]);
    }

    /**
     * @Route("/adapter/{useCase}/{deposit}", name="settlement_adapter_detail")
     */
    public function adapterDetailAction($useCase, Entity\Deposit $deposit, Request $request)
    {
        $templateName = 'ResourceDeposit/'.$useCase.'-deposit-fragment.html.twig';
        return $this->render($templateName, [
            'deposit' => $deposit,
            'adapter' => $deposit->asUseCase($useCase),
        ]);
    }

}
