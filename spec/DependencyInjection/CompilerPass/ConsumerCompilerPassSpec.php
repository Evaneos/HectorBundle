<?php

namespace spec\Evaneos\HectorBundle\DependencyInjection\CompilerPass;

use Evaneos\HectorBundle\DependencyInjection\CompilerPass\ConsumerCompilerPass;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ConsumerCompilerPassSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ConsumerCompilerPass::class);
    }

    public function it_should_process(ContainerBuilder $c, Definition $definition)
    {
        $c->findTaggedServiceIds('hector.consumer')
            ->shouldBeCalled()
            ->willReturn([
                'consumer' => [
                    ['exchange' => 'a', 'queue' => 'queue_a'],
                ],
            ])
        ;

        $definition->setFactory([new Reference('hector.consumer.factory'), 'create'])
            ->shouldBeCalled()
        ;

        $definition->setArguments([
            'default',
            'a',
            'queue_a',
        ])->shouldBeCalled();

        $c->getDefinition('consumer')
            ->shouldBeCalled()
            ->willReturn($definition)
        ;

        $this->process($c);
    }
}
