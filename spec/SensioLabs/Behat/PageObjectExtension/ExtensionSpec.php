<?php

namespace spec\SensioLabs\Behat\PageObjectExtension;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ExtensionSpec extends ObjectBehavior
{
    function let(ContainerBuilder $container, ParameterBagInterface $parameterBag)
    {
        $container->hasExtension(Argument::any())->willReturn(false);
        $container->addResource(Argument::any())->willReturn($container);
        $container->getParameterBag()->willReturn($parameterBag);
        $container->setDefinition(Argument::cetera())->willReturn(null);
        $container->addCompilerPass(Argument::cetera())->willReturn(null);
    }

    function it_provides_a_config_key()
    {
        $this->getConfigKey()->shouldReturn('page_object');
    }

    function it_should_be_a_testwork_extension()
    {
        $this->shouldHaveType('Behat\Testwork\ServiceContainer\Extension');
    }

    function it_should_load_services(ContainerBuilder $container)
    {
        $container->setDefinition('sensio_labs.page_object_extension.page_factory', Argument::any())->shouldBeCalled();
        $container->setDefinition('sensio_labs.page_object_extension.context.initializer', Argument::any())->shouldBeCalled();

        $this->load($container, array())->shouldReturn(null);
    }
}
