<?php
/**
 * Created by PhpStorm.
 * User: troi
 * Date: 20.1.19
 * Time: 13:20
 */

namespace AppBundle\CompilerPass;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DomCrawler\Crawler;

class TechnologyTreeCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        $techTreeFile = realpath($container->getParameter('kernel.project_dir')."/app/Resources/technology-tree.xml");
        $parser = new Crawler(file_get_contents($techTreeFile));

        $useCases = $this->compileUseCases($parser);
        $blueprints = $this->compileBlueprints($parser);
        $colonizationPacks = $this->compileColonies($parser);

        $container->setParameter('use_cases', $useCases);
        $container->setParameter('default_blueprints', $blueprints);
        $container->setParameter('default_colonization_packs', $colonizationPacks);
    }

    /**
     * @param $useCases[]
     * @param string $useCaseName
     * @return string[]
     */
    private function getParents($useCases, $useCaseName) {
        if (!isset($useCases[$useCaseName])) return [];
        $parents = $useCases[$useCaseName]['parents'];
        $parentsWithGrandparents = [];
        foreach ($parents as $parent) {
            $parentsWithGrandparents[] = $parent;
            $parentsWithGrandparents = array_merge($parentsWithGrandparents, $this->getParents($useCases, $parent));
        }
        return array_unique($parentsWithGrandparents);
    }

    /**
     * @param Crawler $parser
     * @return array
     */
    private function compileUseCases(Crawler $parser)
    {
        $useCases = [];
        $parser->filterXPath('//UseCase')->each(function (Crawler $useCaseNode, $i) use (&$useCases) {
            $nodeInfo = [
                'traits' => [],
                'inputResource' => [],
                'inputProduct' => [],
                'outputResource' => [],
                'outputProduct' => [],
                'parents' => [],
            ];
            $useCaseNode->filterXPath('//trait')->each(function (Crawler $node, $i) use (&$nodeInfo) {
                $nodeInfo['traits'][] = $node->text();
            });
            $useCaseNode->filterXPath("//inputResource")->each(function (Crawler $node, $i) use (&$nodeInfo) {
                $nodeInfo['inputResource'][] = $node->attr('ref');
            });
            $useCaseNode->filterXPath("//inputProduct")->each(function (Crawler $node, $i) use (&$nodeInfo) {
                $nodeInfo['inputProduct'][] = $node->attr('blueprint');
            });
            $useCaseNode->filterXPath("//outputResource")->each(function (Crawler $node, $i) use (&$nodeInfo) {
                $nodeInfo['outputResource'][] = $node->attr('ref');
            });
            $useCaseNode->filterXPath("//outputProduct")->each(function (Crawler $node, $i) use (&$nodeInfo) {
                $nodeInfo['outputProduct'][] = $node->attr('blueprint');
            });
            $useCaseNode->filterXPath("//parent")->each(function (Crawler $node, $i) use (&$nodeInfo) {
                $nodeInfo['parents'][] = $node->attr('ref');
            });
            $useCases[$useCaseNode->attr('id')] = $nodeInfo;
        });
        foreach ($useCases as $name => &$useCase) {
            $useCase['parents'] = $this->getParents($useCases, $name);
        }

        // copy parent items to children
        foreach ($useCases as $name => &$useCase) {
            foreach ($useCase['parents'] as $parentName) {
                $parent = $useCases[$parentName];
                foreach ($parent['traits'] as $parentTrait) {
                    $useCase['traits'][] = $parentTrait;
                }
                $useCase['traits'] = array_unique($useCase['traits']);

                foreach ($parent['inputResource'] as $parentTrait) {
                    $useCase['inputResource'][] = $parentTrait;
                }
                $useCase['inputResource'] = array_unique($useCase['inputResource']);

                foreach ($parent['inputProduct'] as $parentTrait) {
                    $useCase['inputProduct'][] = $parentTrait;
                }
                $useCase['inputProduct'] = array_unique($useCase['inputProduct']);

                foreach ($parent['outputResource'] as $parentTrait) {
                    $useCase['outputResource'][] = $parentTrait;
                }
                $useCase['outputResource'] = array_unique($useCase['outputResource']);

                foreach ($parent['outputProduct'] as $parentTrait) {
                    $useCase['outputProduct'][] = $parentTrait;
                }
                $useCase['outputProduct'] = array_unique($useCase['outputProduct']);
            }
        }
        return $useCases;
    }

    private function compileBlueprints(Crawler $parser, array $useCases = [])
    {
        $blueprints = [];
        $parser->filterXPath('//Blueprint')->each(function (Crawler $blueprintNode, $i) use (&$blueprints, $useCases) {
            $nodeInfo = [
                'building_requirements' => [],
                'constraints' => [],
                'useCases' => [],
                'output' => [],
                'technologies' => [],
                'trait_values' => [],
            ];
            if ($blueprintNode->attr('output') != null) {
                $nodeInfo['output'][] = $blueprintNode->attr('output');
            }
            $blueprintNode->filterXPath('//usedAs')->each(function (Crawler $node, $i) use (&$nodeInfo, $useCases) {
                $nodeInfo['useCases'][] = $node->attr('ref');
                if (isset($useCases[$node->attr('ref')]['parents'])) {
                    foreach ($useCases[$node->attr('ref')]['parents'] as $parentUseCase) {
                        $nodeInfo['useCases'][] = $parentUseCase;
                    }
                }
            });
            $blueprintNode->filterXPath('//price/Resource')->each(function (Crawler $node, $i) use (&$nodeInfo) {
                if (!empty($node->attr('ref')) && $node->attr('count') > 0) {
                    $nodeInfo['building_requirements'][$node->attr('ref')] = $node->attr('count');
                }
            });
            $blueprintNode->filterXPath("//price/Product")->each(function (Crawler $node, $i) use (&$nodeInfo) {
                if (!empty($node->attr('ref')) && $node->attr('count') > 0) {
                    $nodeInfo['building_requirements'][$node->attr('ref')] = $node->attr('count');
                }
            });
            $blueprintNode->filterXPath("//constraints/Resource")->each(function (Crawler $node, $i) use (&$nodeInfo) {
                if (!empty($node->attr('ref')) && $node->attr('count') > 0) {
                    $nodeInfo['constraints'][$node->attr('ref')] = $node->attr('count');
                }
            });
            $blueprintNode->filterXPath("//constraints/Product")->each(function (Crawler $node, $i) use (&$nodeInfo) {
                if (!empty($node->attr('ref')) && $node->attr('count') > 0) {
                    $nodeInfo['constraints'][$node->attr('ref')] = $node->attr('count');
                }
            });
            $blueprintNode->filterXPath("//Technology")->each(function (Crawler $node, $i) use (&$nodeInfo) {
                $nodeInfo['technologies'][] = $node->attr('ref');
            });
            $blueprintNode->filterXPath("//traitValue")->each(function (Crawler $node, $i) use (&$nodeInfo) {
                $nodeInfo['trait_values'][$node->attr('ref')] = $node->attr('value');
            });
            $blueprints[$blueprintNode->attr('id')] = $nodeInfo;
        });

        return $blueprints;
    }

    private function compileColonies(Crawler $parser)
    {
        $colonies = [];
        $parser->filterXPath('//ColonizationPack')->each(function (Crawler $colonyPack, $i) use (&$colonies) {
            $nodeInfo = [
                'deposits' => [],
            ];
            $colonyPack->filterXPath('//Resource')->each(function (Crawler $node, $i) use (&$nodeInfo) {
                if (!empty($node->attr('ref')) && $node->attr('count') > 0) {
                    $nodeInfo['deposits'][$node->attr('ref')] = [
                        'amount' => $node->attr('count')
                    ];
                }
            });
            $colonyPack->filterXPath("//Product")->each(function (Crawler $node, $i) use (&$nodeInfo) {
                if (!empty($node->attr('blueprint')) && $node->attr('count') > 0) {
                    $nodeInfo['deposits'][$node->attr('blueprint')] = [
                        'blueprint' => $node->attr('blueprint'),
                        'amount' => $node->attr('count'),
                    ];
                }
            });
            $colonies[$colonyPack->attr('id')] = $nodeInfo;
        });

        return $colonies;
    }

}