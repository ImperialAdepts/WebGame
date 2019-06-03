<?php

namespace PlanetBundle\Controller;

use PlanetBundle\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="deposit")
 */
class ResourceDepositController extends BasePlanetController
{
    /**
     * @Route("/detail/{deposit}", name="settlement_deposit_detail")
     */
    public function depositDetailAction(Entity\ResourceDeposit $deposit, Request $request)
    {
        $adapters = [];
        if ($deposit->getBlueprint() != null) {
            foreach ($deposit->getBlueprint()->getUseCases() as $useCase) {
                // TODO: podminku pouzivat jen na vyvojovem prostredi
//                if (($adapter = $deposit->asUseCase($useCase)) != null) {
                $adapter = $deposit->asUseCase($useCase);
                    $adapters[$useCase] = $adapter;
//                }
            }
        }
        $consumption = $this->get('maintainer')->getConsumption($deposit->getResourceHandler(), $deposit->getResourceDescriptor());
        $production = $this->get('maintainer')->getProduction($deposit->getResourceHandler(), $deposit->getResourceDescriptor());
        return $this->render('ResourceDeposit/resource-deposit-fragment.html.twig', [
            'deposit' => $deposit,
            'consumption' => $consumption,
            'production' => $production,
            'amountChange' => $production - $consumption,
            'adapters' => $adapters,
        ]);
    }

    /**
     * @Route("/adapter/{useCase}/{deposit}", name="settlement_adapter_detail")
     */
    public function adapterDetailAction($useCase, Entity\ResourceDeposit $deposit, Request $request)
    {
        $templateName = 'ResourceDeposit/'.$useCase.'-deposit-fragment.html.twig';
        return $this->render($templateName, [
            'deposit' => $deposit,
            'adapter' => $deposit->asUseCase($useCase),
        ]);
    }

}
