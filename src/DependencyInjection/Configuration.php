<?php

namespace Evaneos\HectorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('hector');

        $rootNode
            ->isRequired()
            ->children()
                ->append($this->addConnectionNode())
                ->append($this->addAmqpExchangesNode())
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition|\Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    protected function addConnectionNode()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('connections');

        $node
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->prototype('array')
                ->children()
                    ->scalarNode('host')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('port')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('login')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('password')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('vhost')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('read_timeout')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('write_timeout')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('connect_timeout')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    /**
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition|\Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    protected function addAmqpExchangesNode()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('exchanges');

        $node
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->scalarNode('type')
                        ->isRequired()
                    ->end()
                    ->scalarNode('flags')
                        ->isRequired()
                    ->end()
                    ->arrayNode('arguments')
                        ->prototype('scalar')
                        ->end()
                    ->end()
                    ->arrayNode('queues')
                        ->prototype('array')
                            ->children()
                                ->integerNode('flags')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('routing_key')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->arrayNode('arguments')
                                    ->prototype('scalar')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
