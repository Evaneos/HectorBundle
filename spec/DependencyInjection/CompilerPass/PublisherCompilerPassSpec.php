<?php

namespace spec\Evaneos\HectorBundle\DependencyInjection\CompilerPass;

use Evaneos\HectorBundle\DependencyInjection\CompilerPass\PublisherCompilerPass;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class PublisherCompilerPassSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(PublisherCompilerPass::class);
    }

    public function it_should_process(ContainerBuilder $c, Definition $definition)
    {
        $c->findTaggedServiceIds('hector.publisher')
          ->shouldBeCalled()
          ->willReturn([
              'publisher' => [
                  ['routing_key_prefix' => 'foo', 'exchange' => 'bar'],
              ],
          ])
        ;

        $definition->setFactory([new Reference('hector.publisher.factory'), 'create'])
            ->shouldBeCalled()
        ;

        $definition->setArguments([
            'default',
            'bar',
            [
                'routing_key_prefix' => 'foo',
            ],
        ])->shouldBeCalled();

        $c->getDefinition('publisher')
          ->shouldBeCalled()
          ->willReturn($definition)
        ;

        $this->process($c);
    }
}
