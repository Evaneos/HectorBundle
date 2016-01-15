<?php

namespace Evaneos\HectorBundle;

use Evaneos\HectorBundle\DependencyInjection\CompilerPass\ConsumerCompilerPass;
use Evaneos\HectorBundle\DependencyInjection\CompilerPass\PublisherCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HectorBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new PublisherCompilerPass());
        $container->addCompilerPass(new ConsumerCompilerPass());
    }
}
