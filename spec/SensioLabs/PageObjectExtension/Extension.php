<?php

namespace spec\SensioLabs\PageObjectExtension;

use PHPSpec2\Matcher\CustomMatchersProviderInterface;
use PHPSpec2\Matcher\InlineMatcher;
use PHPSpec2\ObjectBehavior;

class Extension extends ObjectBehavior implements CustomMatchersProviderInterface
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
            'sensio_labs.page_object_extension.session',
            'sensio_labs.page_object_extension.page_factory',
            'sensio_labs.page_object_extension.context.initializer'
        ));

        $this->load(array(), $container)->shouldReturn(null);
    }

    function it_registeres_namespaces_compiler_pass()
    {
        $this->getCompilerPasses()->shouldHaveCompilerPass('SensioLabs\PageObjectExtension\Compiler\NamespacesPass');
    }

    private function servicesShouldBeRegistered($container, $serviceIds)
    {
        $container->hasExtension(ANY_ARGUMENTS)->willReturn(false);

        foreach ($serviceIds as $id) {
            $container->setDefinition($id, \Mockery::any())->shouldBeCalled();
        }
    }

    static public function getMatchers()
    {
        return array(
            new InlineMatcher('haveCompilerPass', function($subject, $class) {
                foreach ($subject as $pass) {
                    if ($pass instanceof $class) {
                        return true;
                    }
                }

                return false;
            })
        );
    }
}
