<?php

namespace PlanetBundle\Controller;

use AppBundle\Builder\PlanetBuilder;
use AppBundle\Entity;
use PlanetBundle\Entity as PlanetEntity;
use AppBundle\Fixture\ResourceAndBlueprintFixture;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Tracy\Debugger;

/**
 * @Route(path="planet")
 */
class BasePlanetController extends Controller
{
    /** @var Entity\SolarSystem\Planet */
	protected $planet;
	/** @var PlanetEntity\Human */
	protected $human;
	/** @var Entity\Human */
	protected $globalHuman;

    public function init() {
        $this->globalHuman = $this->get('logged_user_settings')->getHuman();
        $this->planet = $this->globalHuman->getPlanet();
        $this->human = $this->getDoctrine()->getManager('planet')
            ->getRepository(PlanetEntity\Human::class)->getByGlobalHuman($this->globalHuman);

        if ($this->human === null) {
            throw new NotFoundHttpException("Human was not found on this planet");
        }
    }


    /**
     * @return Entity\SolarSystem\Planet
     */
    public function getPlanet()
    {
        return $this->planet;
    }

    /**
     * @return PlanetEntity\Human
     */
    public function getHuman()
    {
        return $this->human;
    }

    /**
     * @return Entity\Human
     */
    public function getGlobalHuman()
    {
        return $this->globalHuman;
    }

}
