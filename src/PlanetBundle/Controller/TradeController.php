<?php

namespace PlanetBundle\Controller;

use AppBundle\Builder\PlanetBuilder;
use AppBundle\Descriptor\Adapters;
use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\UseCaseEnum;
use PlanetBundle\Entity;
use AppBundle\Repository\JobRepository;
use PlanetBundle\Repository\RegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Tracy\Debugger;

/**
 * @Route(path="trade")
 */
class TradeController extends BasePlanetController
{

    /**
     * @Route("/{settlement}/trading", name="trade_list")
     */
    public function tradingAction(Entity\Settlement $settlement, Request $request)
    {
        $offers = $this->getDoctrine()->getManager('planet')->getRepository(Entity\TradeOffer::class)->findBy(['settlement'=>$settlement]);

        return $this->render('Settlement/trading.html.twig', [
            'settlement' => $settlement,
            'offers' => $offers,
        ]);
    }

    /**
     * @Route("/sell-region/{deposit}", name="trade_make_offer_from_region")
     */
    public function sellFromRegionAction(Entity\RegionResourceDeposit $deposit, Request $request)
    {
        $portables = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::PORTABLES);
        return $this->render('Trade/make-offer.html.twig', [
            'settlement' => $deposit->getRegion()->getSettlement(),
            'priceBlueprints' => $portables,
            'deposit' => $deposit,
        ]);
    }

    /**
     * @Route("/sell/{deposit}", name="trade_make_offer")
     */
    public function sellAction(Entity\PeakResourceDeposit $deposit, Request $request)
    {
        $portables = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::PORTABLES);
        return $this->render('Trade/make-offer.html.twig', [
            'settlement' => $deposit->getPeak()->getSettlement(),
            'priceBlueprints' => $portables,
            'deposit' => $deposit,
        ]);
    }

    /**
     * @Route("/set-offer-from-region/{deposit}/{blueprint}/{count}", name="trade_set_offer_from_region")
     */
    public function setRegionOfferAction(Entity\RegionResourceDeposit $deposit, Entity\Blueprint $blueprint, $count, Request $request)
    {
        $deposit->setAmount($deposit->getAmount() - $count);
        $this->getDoctrine()->getManager('planet')->persist($deposit);

        $peak = $deposit->getRegion()->getSettlement()->getAdministrativeCenter();
        if ($peak == null) {
            $peak = $deposit->getRegion()->getPeakCenter();
        }
        $peak->addResourceDeposit($deposit->getBlueprint(), $count);
        $this->getDoctrine()->getManager('planet')->persist($peak);

        $offer = new Entity\TradeOffer();
        $offer->setBlueprint($blueprint);
        $offer->setAmountRequested($count);
        $offer->setAmountRequested($count);
        $offer->setOfferedResourceDeposit($peak->getResourceDeposit($deposit->getResourceDescriptor()));
        $offer->setSettlement($deposit->getRegion()->getSettlement());
        $this->getDoctrine()->getManager('planet')->persist($offer);
        $this->getDoctrine()->getManager('planet')->flush();

        return $this->redirectToRoute('trade_list', [
            'settlement' => $deposit->getRegion()->getSettlement()->getId(),
        ]);
    }

    /**
     * @Route("/set-offer/{deposit}/{blueprint}/{count}", name="trade_set_offer")
     */
    public function setOfferAction(Entity\PeakResourceDeposit $deposit, Entity\Blueprint $blueprint, $count, Request $request)
    {
        $offer = new Entity\TradeOffer();
        $offer->setBlueprint($blueprint);
        $offer->setAmountRequested($count);
        $offer->setAmountRequested($count);
        $offer->setOfferedResourceDeposit($deposit);
        $offer->setSettlement($deposit->getPeak()->getSettlement());
        $this->getDoctrine()->getManager('planet')->persist($offer);
        $this->getDoctrine()->getManager('planet')->flush();

        return $this->redirectToRoute('trade_list', [
            'settlement' => $deposit->getPeak()->getSettlement()->getId(),
        ]);
    }
}
