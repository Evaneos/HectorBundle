<?php

namespace Evaneos\HectorBundle\DependencyInjection;

use Evaneos\Hector\Connection\Connection;
use Evaneos\Hector\Exchange\Context as ExchangeContext;
use Evaneos\Hector\Queue\Context as QueueContext;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class HectorExtension extends Extension
{
    /** @var  ContainerBuilder */
    private $container;

    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->container = $container;
        $configuration   = new Configuration();
        $config          = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.yml');

        $connectionsConfig = $config['connections'];
        $exchangesConfig   = $config['exchanges'];

        $container->setParameter('hector.config.connections', $connectionsConfig);
        $container->setParameter('hector.config.exchanges', $exchangesConfig);

        $contextRegistry = $container->getDefinition('hector.context.registry');

        foreach ($exchangesConfig as $exchangeName => $exchangeConfiguration) {
            $exchangeContextServiceName = 'hector.exchange.context.' . $exchangeName;
            $exchangeContext            = new Definition(ExchangeContext::class, [$exchangeConfiguration]);
            $exchangeContext->setFactory('Evaneos\Hector\Exchange\Context::createFromConfig');
            $container->setDefinition($exchangeContextServiceName, $exchangeContext);
            $contextRegistry->addMethodCall('addExchangeContext', [$exchangeName, new Reference($exchangeContextServiceName)]);

            if (!isset($exchangeConfiguration['queues'])) {
                continue;
            }

            foreach ($exchangeConfiguration['queues'] as $queueName => $queueConfiguration) {
                $queueContextServiceName = 'hector.queue.context.' . $queueName;
                $queueContext            = new Definition(QueueContext::class, [$queueConfiguration]);
                $queueContext->setFactory('Evaneos\Hector\Queue\Context::createFromConfig');
                $container->setDefinition($queueContextServiceName, $queueContext);
                $contextRegistry->addMethodCall('addQueueContext', [$queueName, new Reference($queueContextServiceName)]);
            }
        }

        // Register Connections
        foreach ($config['connections'] as $name => $connectionConfig) {
            $this->registerConnection($name, $connectionConfig);
        }
    }

    /**
     * @param string $name
     * @param array  $config
     */
    private function registerConnection($name, array $config)
    {
        $connectionServiceName = 'hector.connection.' . $name;

        $connectionDef = (new Definition(Connection::class, [$name, $config]))
            ->setFactory([new Reference('hector.connection.factory'), 'createNamed'])
        ;

        $this->container->setDefinition($connectionServiceName, $connectionDef);

        $registry = $this->container->getDefinition('hector.connection.registry');
        $registry->addMethodCall('addConnection', [new Reference($connectionServiceName)]);
    }
}
