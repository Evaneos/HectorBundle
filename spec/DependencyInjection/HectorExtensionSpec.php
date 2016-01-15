<?php

namespace spec\Evaneos\HectorBundle\DependencyInjection;

use Evaneos\HectorBundle\DependencyInjection\HectorExtension;
use PhpSpec\ObjectBehavior;

class HectorExtensionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(HectorExtension::class);
    }
}
