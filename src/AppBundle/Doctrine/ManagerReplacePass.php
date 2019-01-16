<?php

namespace AppBundle\Doctrine;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ManagerReplacePass implements CompilerPassInterface
{

	/**
	 * @param ContainerBuilder $container
	 */
	public function process(ContainerBuilder $container)
	{
		$container->setParameter('doctrine.orm.entity_manager.class', UuidEntityManager::class);
		$definition = $container->findDefinition('doctrine.orm.entity_manager');
		$container->removeDefinition('doctrine.orm.entity_manager');
		$container->removeAlias('doctrine.orm.entity_manager');
		$definition->setClass(UuidEntityManager::class);
		$container->setDefinition('doctrine.orm.entity_manager', $definition);
	}
}