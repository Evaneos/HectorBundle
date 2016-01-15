<?php

namespace spec\Evaneos\HectorBundle\DependencyInjection;

use Evaneos\HectorBundle\DependencyInjection\Configuration;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ConfigurationSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Configuration::class);
    }

    public function it_should_give_config_tree()
    {
        $this->getConfigTreeBuilder()->shouldBeAnInstanceOf(TreeBuilder::class);
    }
}
