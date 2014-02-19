<?php

namespace spec\SensioLabs\Behat\PageObjectExtension;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ExtensionSpec extends ObjectBehavior
{
    function it_should_be_a_behat_extension()
    {
        $this->shouldHaveType('Behat\Testwork\ServiceContainer\Extension');
    }

    function it_should_load_services(ContainerBuilder $container, ParameterBagInterface $parameterBag)
    {
        $this->servicesShouldBeRegistered($container, array(
            'sensio_labs.page_object_extension.page_factory',
            'sensio_labs.page_object_extension.context.initializer'
        ), $parameterBag);

        $config = array(
            'namespaces' => array(
                'page'    => 'Page\\',
                'element' => 'Page\\Element\\'
            )
        );

        $this->load($container, $config)->shouldReturn(null);
    }

    private function servicesShouldBeRegistered($container, $serviceIds, $parameterBag)
    {
        $container->hasExtension(Argument::any())->willReturn(false);
        $container->addResource(Argument::any())->willReturn($container);
        $container->getParameterBag()->willReturn($parameterBag);

        foreach ($serviceIds as $id) {
            $container->setDefinition($id, Argument::any())->shouldBeCalled();
        }
    }
}
