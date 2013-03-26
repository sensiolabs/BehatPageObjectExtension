<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\Compiler;

use PHPSpec2\ObjectBehavior;

class NamespacesPass extends ObjectBehavior
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    function let($container)
    {
    }

    function it_should_set_global_namespace_if_context_class_is_not_defined($container)
    {
        $this->givenContainer($container, array(
            'behat.context.class' => null,
            'sensio_labs.page_object_extension.namespaces.page' => null,
            'sensio_labs.page_object_extension.namespaces.element' => null
        ));

        $this->expectContainer($container, array(
            'sensio_labs.page_object_extension.namespaces.page' => '\\',
            'sensio_labs.page_object_extension.namespaces.element' => '\\'
        ));

        $this->process($container)->shouldReturn(null);
    }

    function it_should_set_global_namespace_with_a_non_namespaced_context($container)
    {
        $this->givenContainer($container, array(
            'behat.context.class' => 'FeatureContext',
            'sensio_labs.page_object_extension.namespaces.page' => null,
            'sensio_labs.page_object_extension.namespaces.element' => null
        ));

        $this->expectContainer($container, array(
            'sensio_labs.page_object_extension.namespaces.page' => '\\',
            'sensio_labs.page_object_extension.namespaces.element' => '\\'
        ));

        $this->process($container)->shouldReturn(null);
    }

    function it_should_automatically_populate_namespace_parameters($container)
    {
        $this->givenContainer($container, array(
            'behat.context.class' => 'Features\Context\FeatureContext',
            'sensio_labs.page_object_extension.namespaces.page' => null,
            'sensio_labs.page_object_extension.namespaces.element' => null
        ));

        $this->expectContainer($container, array(
            'sensio_labs.page_object_extension.namespaces.page' => 'Features\Context\Page',
            'sensio_labs.page_object_extension.namespaces.element' => 'Features\Context\Page\Element'
        ));

        $this->process($container)->shouldReturn(null);
    }

    function it_should_not_overwrite_the_namespace_if_it_is_provided_in_the_configuration($container)
    {
        $this->givenContainer($container, array(
            'behat.context.class' => 'Features\Context\FeatureContext',
            'sensio_labs.page_object_extension.namespaces.page' => 'Features\Pages',
            'sensio_labs.page_object_extension.namespaces.element' => 'Features\Pages\Element'
        ));

        $container->setParameter()->shouldNotBeCalled();

        $this->process($container)->shouldReturn(null);
    }

    function it_should_automatically_populate_element_namespace_if_not_provided($container)
    {
        $this->givenContainer($container, array(
            'behat.context.class' => 'Features\Context\FeatureContext',
            'sensio_labs.page_object_extension.namespaces.page' => 'Features\Pages',
            'sensio_labs.page_object_extension.namespaces.element' => null
        ));

        $container->setParameter('sensio_labs.page_object_extension.namespaces.page', ANY_ARGUMENT)->shouldNotBeCalled();
        $container->setParameter('sensio_labs.page_object_extension.namespaces.element', 'Features\Pages\Element')->shouldBeCalled();

        $this->process($container)->shouldReturn(null);
    }

    function it_should_not_subnamespace_element_if_page_is_in_global_namespace($container)
    {
        $this->givenContainer($container, array(
            'behat.context.class' => 'Features\Context\FeatureContext',
            'sensio_labs.page_object_extension.namespaces.page' => '\\',
            'sensio_labs.page_object_extension.namespaces.element' => null
        ));

        $container->setParameter('sensio_labs.page_object_extension.namespaces.page', ANY_ARGUMENT)->shouldNotBeCalled();
        $container->setParameter('sensio_labs.page_object_extension.namespaces.element', '\\')->shouldBeCalled();

        $this->process($container)->shouldReturn(null);
    }

    function it_should_not_overwrite_element_if_page_is_not_defined($container)
    {
        $this->givenContainer($container, array(
            'behat.context.class' => 'Features\Context\FeatureContext',
            'sensio_labs.page_object_extension.namespaces.page' => null,
            'sensio_labs.page_object_extension.namespaces.element' => '\\Element'
        ));

        $container->setParameter('sensio_labs.page_object_extension.namespaces.page', 'Features\Context\Page')->shouldBeCalled();
        $container->setParameter('sensio_labs.page_object_extension.namespaces.element', ANY_ARGUMENT)->shouldNotBeCalled();

        $this->process($container)->shouldReturn(null);
    }

    private function givenContainer($container, $parameters)
    {
        foreach ($parameters as $id => $value) {
            $container->getParameter($id)->willReturn($value);
        }
    }

    private function expectContainer($container, $parameters)
    {
        foreach ($parameters as $id => $value) {
            $container->setParameter($id, $value)->shouldBeCalled();
        }
    }
}
