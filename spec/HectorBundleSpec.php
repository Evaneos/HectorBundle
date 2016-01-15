<?php

namespace spec\Evaneos\HectorBundle;

use Evaneos\HectorBundle\DependencyInjection\CompilerPass\ConsumerCompilerPass;
use Evaneos\HectorBundle\DependencyInjection\CompilerPass\PublisherCompilerPass;
use Evaneos\HectorBundle\HectorBundle;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class HectorBundleSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(HectorBundle::class);
    }

    public function it_should_build(ContainerBuilder $container)
    {
        $container->addCompilerPass(Argument::type(PublisherCompilerPass::class))->shouldBeCalled();
        $container->addCompilerPass(Argument::type(ConsumerCompilerPass::class))->shouldBeCalled();

        $this->build($container);
    }
}
