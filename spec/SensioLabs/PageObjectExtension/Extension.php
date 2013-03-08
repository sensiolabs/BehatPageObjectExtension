<?php

namespace spec\SensioLabs\PageObjectExtension;

use PHPSpec2\ObjectBehavior;

class Extension extends ObjectBehavior
{
    function it_should_be_a_behat_extension()
    {
        $this->shouldHaveType('Behat\Behat\Extension\ExtensionInterface');
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    function it_should_load_services($container)
    {
        $this->servicesShouldBeRegistered($container, array(
            'sensio.page_object_extension.session',
            'sensio.page_object_extension.page_factory',
            'sensio.page_object_extension.context.initializer'
        ));

        $this->load(array(), $container)->shouldReturn(null);
    }

    function it_has_no_compiler_passes()
    {
        $this->getCompilerPasses()->shouldReturn(array());
    }

    private function servicesShouldBeRegistered($container, $serviceIds)
    {
        $container->hasExtension(ANY_ARGUMENTS)->willReturn(false);

        foreach ($serviceIds as $id) {
            $container->setDefinition($id, \Mockery::any())->shouldBeCalled();
        }
    }
}
