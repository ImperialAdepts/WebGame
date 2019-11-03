<?php

namespace PlanetBundle\Controller;

use AppBundle\Builder\PlanetBuilder;
use AppBundle\Entity\Human\EventDataTypeEnum;
use AppBundle\Entity\Human\EventTypeEnum;
use PlanetBundle\Entity;
use AppBundle\Repository\JobRepository;
use PlanetBundle\Repository\RegionRepository;
use PlanetBundle\UseCase\Portable;
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
        $offers = $this->getDoctrine()->getManager('planet')->getRepository(Entity\TradeOffer::class)->findBy([
            'settlement'=>$settlement
        ]);
        $requests = $this->getDoctrine()->getManager('planet')->getRepository(Entity\TradeOffer::class)->findBy([
            'settlement'=>$settlement
        ]);

        return $this->render('Settlement/trading.html.twig', [
            'settlement' => $settlement,
            'offers' => $offers,
            'requests' => $requests,
        ]);
    }

    /**
     * @Route("/sell/{deposit}", name="trade_make_offer")
     */
    public function sellAction(Entity\Deposit $deposit, Request $request)
    {
        $portables = $this->getDoctrine()->getManager('planet')->getRepository(Entity\Resource\Blueprint::class)->getByUseCase(Portable::class);
        return $this->render('Trade/make-offer.html.twig', [
            'settlement' => $deposit->getResourceHandler()->getSettlement(),
            'priceBlueprints' => $portables,
            'deposit' => $deposit,
        ]);
    }

    /**
     * @Route("/set-offer/{deposit}/{blueprint}/{count}", name="trade_set_offer")
     */
    public function setOfferAction(Entity\Deposit $deposit, Entity\Resource\Blueprint $blueprint, $count, Request $request)
    {
        $offer = new Entity\TradeOffer();
        $offer->setBlueprint($blueprint);
        $offer->setAmountRequested($count);
        $offer->setOfferedResourceDeposit($deposit);
        $offer->setSettlement($deposit->getResourceHandler()->getSettlement());
        $this->getDoctrine()->getManager('planet')->persist($offer);
        $this->getDoctrine()->getManager('planet')->flush();

        $this->createEvent(EventTypeEnum::TRADE_SELL, [
            EventDataTypeEnum::BLUEPRINT => $offer->getOfferedResourceDeposit()->getBlueprint(),
            EventDataTypeEnum::PRICE => [
                $offer->getBlueprint()->getResourceDescriptor() => $offer->getAmountRequested(),
            ],
        ]);

        return $this->redirectToRoute('trade_list', [
            'settlement' => $deposit->getPeak()->getSettlement()->getId(),
        ]);
    }

    /**
     * @Route("/buy/{offer}", name="trade_buy")
     */
    public function buyAction(Entity\TradeOffer $offer, Request $request)
    {
        /** @var Entity\Settlement $settlement */
        $settlement = $this->getHuman()->getCurrentPeakPosition()->getSettlement();
        $settlement->addResourceDeposit($offer->getOfferedResourceDeposit()->getBlueprint(), $offer->getOfferedResourceDeposit()->getAmount());
        $settlement->consumeResourceDepositAmount($offer->getBlueprint()->getResourceDescriptor(), $offer->getAmountRequested());
        $this->getDoctrine()->getManager('planet')->persist($settlement);
        $this->getDoctrine()->getManager('planet')->remove($offer);
        $this->getDoctrine()->getManager('planet')->flush();

        $this->createEvent(EventTypeEnum::TRADE_BUY, [
            EventDataTypeEnum::BLUEPRINT => $offer->getOfferedResourceDeposit()->getBlueprint(),
            EventDataTypeEnum::PRICE => [
                $offer->getBlueprint()->getResourceDescriptor() => $offer->getAmountRequested(),
            ],
        ]);

        return $this->redirectToRoute('trade_list', [
            'settlement' => $settlement->getId(),
        ]);
    }
}
