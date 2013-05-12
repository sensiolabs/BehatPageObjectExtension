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
        $this->shouldHaveType('Behat\Behat\Extension\ExtensionInterface');
    }

    function it_should_load_services(ContainerBuilder $container, ParameterBagInterface $parameterBag)
    {
        $this->servicesShouldBeRegistered($container, array(
            'sensio_labs.page_object_extension.page_factory',
            'sensio_labs.page_object_extension.context.initializer'
        ), $parameterBag);

        $this->load(array(), $container)->shouldReturn(null);
    }

    function it_registers_namespaces_compiler_pass()
    {
        $this->getCompilerPasses()->shouldHaveCompilerPass('SensioLabs\Behat\PageObjectExtension\Compiler\NamespacesPass');
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

    public function getMatchers()
    {
        return array(
            'haveCompilerPass' => function($subject, $class) {
                foreach ($subject as $pass) {
                    if ($pass instanceof $class) {
                        return true;
                    }
                }

                return false;
            }
        );
    }
}
