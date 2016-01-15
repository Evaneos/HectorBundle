<?php

namespace Evaneos\HectorBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PublisherCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds('hector.publisher');

        foreach ($taggedServices as $id => $attributes) {
            $parameters = $attributes[0];

            // If no specific connection, fallback on default
            if (!isset($parameters['connection'])) {
                $parameters['connection'] = 'default';
            }

            if (!isset($parameters['routing_key_prefix'])) {
                $parameters['routing_key_prefix'] = '';
            }

            $publisher = $container->getDefinition($id);
            $publisher->setFactory([new Reference('hector.publisher.factory'), 'create']);
            $publisher->setArguments([
                $parameters['connection'],
                $parameters['exchange'],
                [
                    'routing_key_prefix' => $parameters['routing_key_prefix'],
                ],
            ]);
        }
    }
}
