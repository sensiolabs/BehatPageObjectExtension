<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\ServiceContainer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PageObjectExtensionSpec extends ObjectBehavior
{
    function let(ContainerBuilder $container, ParameterBagInterface $parameterBag)
    {
        $container->hasExtension(Argument::any())->willReturn(false);
        $container->addResource(Argument::any())->willReturn($container);
        $container->getParameterBag()->willReturn($parameterBag);
        $container->setDefinition(Argument::cetera())->willReturn(null);
        $container->setParameter(Argument::cetera())->willReturn(null);
        $container->addCompilerPass(Argument::cetera())->willReturn(null);
        $container->setAlias(Argument::cetera())->willReturn(null);
        if (method_exists(ContainerBuilder::class, 'fileExists')) {
            $container->fileExists(Argument::any())->willReturn(false);
        }
        if (method_exists(ContainerBuilder::class, 'addRemovedBindingIds')) {
            $container->addRemovedBindingIds(Argument::any())->willReturn();
        }
        if (method_exists(ContainerBuilder::class, 'removeBindings')) {
            $container->removeBindings(Argument::any())->willReturn();
        }
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
        $container->setDefinition('sensio_labs.page_object_extension.page_factory.default', Argument::any())->shouldBeCalled();
        $container->setDefinition('sensio_labs.page_object_extension.context.initializer', Argument::any())->shouldBeCalled();

        $this->load($container, array())->shouldReturn(null);
    }
}
