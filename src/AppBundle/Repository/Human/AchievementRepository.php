<?php
namespace AppBundle\Repository\Human;

use AppBundle\Entity;
use AppBundle\Entity\Human\AchievementSpaceTypeEnum;
use AppBundle\Entity\Human\AchievementTimeTypeEnum;
use AppBundle\Entity\Human\AchievementTypeEnum;
use PlanetBundle\Entity as PlanetEntity;

class AchievementRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return \stdClass[]
     */
    public function getAchievementCombinations() {
        return [
            $this->createCombination(
                AchievementTypeEnum::LONGEST_LIFE,
                AchievementTimeTypeEnum::ONE_TIME,
                AchievementSpaceTypeEnum::PLANET
            ),
            $this->createCombination(
                AchievementTypeEnum::LONGEST_LIFE,
                AchievementTimeTypeEnum::ONE_TIME,
                AchievementSpaceTypeEnum::UNIVERSE
            ),
            $this->createCombination(
                AchievementTypeEnum::LONGEST_LIFE,
                AchievementTimeTypeEnum::ONE_TIME,
                AchievementSpaceTypeEnum::STATE
            ),
            $this->createCombination(
                AchievementTypeEnum::LONGEST_LIFE,
                AchievementTimeTypeEnum::BY_DECADE,
                AchievementSpaceTypeEnum::PLANET
            ),
            $this->createCombination(
                AchievementTypeEnum::LONGEST_LIFE,
                AchievementTimeTypeEnum::BY_DECADE,
                AchievementSpaceTypeEnum::UNIVERSE
            ),
            $this->createCombination(
                AchievementTypeEnum::LONGEST_LIFE,
                AchievementTimeTypeEnum::BY_DECADE,
                AchievementSpaceTypeEnum::STATE
            ),
            $this->createCombination(
                AchievementTypeEnum::LONGEST_LIFE,
                AchievementTimeTypeEnum::BY_DECADE,
                AchievementSpaceTypeEnum::FAMILY
            ),
            $this->createCombination(
                AchievementTypeEnum::MOST_EMOTIONAL,
                AchievementTimeTypeEnum::BY_PHASE,
                AchievementSpaceTypeEnum::PLANET
            ),
            $this->createCombination(
                AchievementTypeEnum::MOST_EMOTIONAL,
                AchievementTimeTypeEnum::BY_CYCLE,
                AchievementSpaceTypeEnum::PLANET
            ),
            $this->createCombination(
                AchievementTypeEnum::MOST_EMOTIONAL,
                AchievementTimeTypeEnum::BY_DECADE,
                AchievementSpaceTypeEnum::PLANET
            ),
            $this->createCombination(
                AchievementTypeEnum::MOST_EMOTIONAL,
                AchievementTimeTypeEnum::BY_PHASE,
                AchievementSpaceTypeEnum::UNIVERSE
            ),
            $this->createCombination(
                AchievementTypeEnum::MOST_EMOTIONAL,
                AchievementTimeTypeEnum::BY_CYCLE,
                AchievementSpaceTypeEnum::UNIVERSE
            ),
            $this->createCombination(
                AchievementTypeEnum::MOST_EMOTIONAL,
                AchievementTimeTypeEnum::BY_DECADE,
                AchievementSpaceTypeEnum::UNIVERSE
            ),
            $this->createCombination(
                AchievementTypeEnum::MOST_EMOTIONAL,
                AchievementTimeTypeEnum::BY_PHASE,
                AchievementSpaceTypeEnum::UNIVERSE
            ),
            $this->createCombination(
                AchievementTypeEnum::MOST_EMOTIONAL,
                AchievementTimeTypeEnum::BY_CYCLE,
                AchievementSpaceTypeEnum::UNIVERSE
            ),
            $this->createCombination(
                AchievementTypeEnum::MOST_EMOTIONAL,
                AchievementTimeTypeEnum::BY_DECADE,
                AchievementSpaceTypeEnum::UNIVERSE
            ),
        ];
    }

    public function getMostHappyHuman() {
        $result =  $this->getEntityManager()
            ->createQuery(
                'SELECT h FROM AppBundle\Entity\Human h, AppBundle\Entity\Human\Feelings f WHERE h = f.human ORDER BY f.thisTimeHappiness DESC'
            )
            ->getResult();
        return $result[0];
    }

    public function getMostSadHuman() {
        $result =  $this->getEntityManager()
            ->createQuery(
                'SELECT h FROM AppBundle\Entity\Human h, AppBundle\Entity\Human\Feelings f WHERE h = f.human ORDER BY f.thisTimeSadness DESC'
            )
            ->getResult();
        return $result[0];
    }

    public function getMostEmotionalHuman() {
        $result = $this->getEntityManager()
            ->createQuery(
                'SELECT h, (f.thisTimeHappiness + f.thisTimeSadness) AS emotions FROM AppBundle\Entity\Human h, AppBundle\Entity\Human\Feelings f WHERE h = f.human ORDER BY emotions DESC'
            )
            ->getResult();
        return $result[0][0];
    }

    private function createCombination($type, $time, $space) {
        $combo = new \stdClass();
        $combo->type = $type;
        $combo->time = $time;
        $combo->space = $space;
        return $combo;
    }
}