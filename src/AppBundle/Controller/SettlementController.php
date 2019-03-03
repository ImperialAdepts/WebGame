<?php

namespace AppBundle\Controller;

use AppBundle\Descriptor\ResourceDescriptorEnum;
use AppBundle\Descriptor\UseCaseEnum;
use AppBundle\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="settlement")
 */
class SettlementController extends Controller
{
	/**
	 * @Route("/dashboard/{settlement}", name="settlement_dashboard")
	 */
	public function buildAction(Entity\Planet\Settlement $settlement, Request $request)
	{
        /** @var PlanetBuilder $builder */
        $builder = $this->get('planet_builder');
        /** @var Entity\Planet\Region $region */
        foreach ($settlement->getRegions() as $region) {
            // TODO: zmenit na aktualne prihlaseneho cloveka
            $blueprintsByRegions[$region->getCoords()] = $builder->getAvailableBlueprints($region, $settlement->getManager());
        }

        return $this->render('Settlement/dashboard.html.twig', [
            'settlement' => $settlement,
            'buildingBlueprints' => $blueprintsByRegions,
            'human' => $settlement->getManager(),
        ]);
	}

    /**
     * @Route("/settlement_warehouses/{settlement}/{human}", name="settlement_warehouses")
     */
    public function warehouseContentAction(Entity\Planet\Settlement $settlement, Entity\Human $human, Request $request)
    {
        $blueprints = $this->getDoctrine()->getManager()->getRepository(Entity\Blueprint::class)->getWarehouseable();
        $resourceDescriptors = [];
        /** @var Entity\Blueprint $blueprint */
        foreach ($blueprints as $blueprint) {
            $resourceDescriptors[$blueprint->getResourceDescriptor()] = null;
        }
        $warehouseContent = [];
        foreach ($settlement->getResourceDeposits() as $deposit) {
            if (array_key_exists($deposit->getResourceDescriptor(), $resourceDescriptors)) {
                $warehouseContent[] = $deposit;
            }
        }

        return $this->render('Settlement/warehouse-content-fragment.html.twig', [
            'resources' => $warehouseContent,
            'human' => $human,
        ]);
    }

    /**
     * @Route("/settlement_buildings/{settlement}/{human}", name="settlement_buildings")
     */
    public function buildingsAction(Entity\Planet\Settlement $settlement, Entity\Human $human, Request $request)
    {
        $blueprints = $this->getDoctrine()->getManager()->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::LAND_BUILDING);
        $resourceDescriptors = [];
        /** @var Entity\Blueprint $blueprint */
        foreach ($blueprints as $blueprint) {
            $resourceDescriptors[$blueprint->getResourceDescriptor()] = null;
        }
        $buldings = [];
        foreach ($settlement->getResourceDeposits() as $deposit) {
            if (array_key_exists($deposit->getResourceDescriptor(), $resourceDescriptors)) {
                $buldings[] = $deposit;
            }
        }

        return $this->render('Settlement/buildings-fragment.html.twig', [
            'buildings' => $buldings,
            'human' => $human,
        ]);
    }

    /**
     * @Route("/settlement_housing/{settlement}/{human}", name="settlement_housing")
     */
    public function housingAction(Entity\Planet\Settlement $settlement, Entity\Human $human, Request $request)
    {
        $blueprints = $this->getDoctrine()->getManager()->getRepository(Entity\Blueprint::class)->getByUseCase(UseCaseEnum::LIVING_BUILDINGS);
        $resourceDescriptors = [];
        /** @var Entity\Blueprint $blueprint */
        foreach ($blueprints as $blueprint) {
            $resourceDescriptors[$blueprint->getResourceDescriptor()] = null;
        }
        $buldings = [];
        foreach ($settlement->getResourceDeposits() as $deposit) {
            if (array_key_exists($deposit->getResourceDescriptor(), $resourceDescriptors)) {
                $buldings[] = $deposit;
            }
        }
        $peopleCount = 0;
        if ($settlement->getResourceDeposit(ResourceDescriptorEnum::PEOPLE) != null) {
            $peopleCount = $settlement->getResourceDeposit(ResourceDescriptorEnum::PEOPLE)->getAmount();
        }

        $housingCapacity = 0;
        /** @var Entity\ResourceDeposit $bulding */
        foreach ($buldings as $bulding) {
            // TODO: nahradit konstantu kapacitou budovy
            $housingCapacity += $bulding->getAmount()*10;
        }

        $foodEnergy = 0;
        if ($settlement->getResourceDeposit(ResourceDescriptorEnum::SIMPLE_FOOD) != null) {
            $foodEnergy = $settlement->getResourceDeposit(ResourceDescriptorEnum::SIMPLE_FOOD)->getAmount();
        }

        return $this->render('Settlement/housing.html.twig', [
            'people' => $peopleCount,
            'foodEnergy' => $foodEnergy,
            'housingCapacity' => $housingCapacity,
            'buildings' => $buldings,
            'human' => $human,
        ]);
    }
}
