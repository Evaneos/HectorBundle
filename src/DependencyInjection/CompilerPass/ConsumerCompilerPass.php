<?php

namespace Evaneos\HectorBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ConsumerCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds('hector.consumer');

        foreach ($taggedServices as $id => $attributes) {
            $parameters = $attributes[0];

            // If no specific connection, fallback on default
            if (!isset($parameters['connection'])) {
                $parameters['connection'] = 'default';
            }

            $consumer = $container->getDefinition($id);

            $consumer->setFactory([new Reference('hector.consumer.factory'), 'create']);
            $consumer->setArguments([
                $parameters['connection'],
                $parameters['exchange'],
                $parameters['queue'],
            ]);
        }
    }
}
