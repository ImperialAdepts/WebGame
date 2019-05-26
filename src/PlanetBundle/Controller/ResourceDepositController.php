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
     * @Route("/detail/r{deposit}", name="settlement_region_deposit_detail")
     */
    public function regionDepositDetailAction(Entity\RegionResourceDeposit $deposit, Request $request)
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
        $consumption = $this->get('maintainer')->getConsumption($deposit->getRegion(), $deposit->getResourceDescriptor());
        $production = $this->get('maintainer')->getProduction($deposit->getRegion(), $deposit->getResourceDescriptor());
        return $this->render('ResourceDeposit/resource-deposit-fragment.html.twig', [
            'deposit' => $deposit,
            'consumption' => $consumption,
            'production' => $production,
            'amountChange' => $production - $consumption,
            'adapters' => $adapters,
        ]);
    }

    /**
     * @Route("/detail/p{deposit}", name="settlement_peak_deposit_detail")
     */
    public function peakDepositDetailAction(Entity\PeakResourceDeposit $deposit, Request $request)
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
        $consumption = $this->get('maintainer')->getConsumption($deposit->getPeak(), $deposit->getResourceDescriptor());
        $production = $this->get('maintainer')->getProduction($deposit->getPeak(), $deposit->getResourceDescriptor());
        return $this->render('ResourceDeposit/resource-deposit-fragment.html.twig', [
            'deposit' => $deposit,
            'consumption' => $consumption,
            'production' => $production,
            'amountChange' => $production - $consumption,
            'adapters' => $adapters,
        ]);
    }

    /**
     * @Route("/adapter/{useCase}/r{deposit}", name="settlement_region_adapter_detail")
     */
    public function adapterRegionDetailAction($useCase, Entity\RegionResourceDeposit $deposit, Request $request)
    {
        $templateName = 'ResourceDeposit/'.$useCase.'-deposit-fragment.html.twig';
        return $this->render($templateName, [
            'deposit' => $deposit,
            'adapter' => $deposit->asUseCase($useCase),
        ]);
    }

    /**
     * @Route("/adapter/{useCase}/p{deposit}", name="settlement_peak_adapter_detail")
     */
    public function adapterPeakDetailAction($useCase, Entity\RegionResourceDeposit $deposit, Request $request)
    {
        $templateName = 'ResourceDeposit/'.$useCase.'-deposit-fragment.html.twig';
        return $this->render($templateName, [
            'deposit' => $deposit,
            'adapter' => $deposit->asUseCase($useCase),
        ]);
    }
}
