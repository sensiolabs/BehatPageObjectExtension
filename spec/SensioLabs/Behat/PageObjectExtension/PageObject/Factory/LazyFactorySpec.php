<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\PageObject\Factory;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use ProxyManager\Factory\AbstractLazyFactory;
use ProxyManager\Proxy\LazyLoadingInterface;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;
use SensioLabs\Behat\PageObjectExtension\PageObject\InlineElement;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use SensioLabs\Behat\PageObjectExtension\PageObject\PageObject;

class LazyFactorySpec extends ObjectBehavior
{
    function let(Factory $decoratedFactory, AbstractLazyFactory $proxyFactory)
    {
        $this->beConstructedWith($decoratedFactory, $proxyFactory);
    }

    function it_is_a_page_object_factory()
    {
        $this->shouldHaveType('SensioLabs\Behat\PageObjectExtension\PageObject\Factory');
    }

    function it_delegates_create_page_calls_to_the_decorated_factory(Factory $decoratedFactory, Page $page)
    {
        $decoratedFactory->createPage('Foo')->willReturn($page);

        $this->createPage('Foo')->shouldReturn($page);
    }

    function it_delegates_create_element_calls_to_the_decorated_factory(Factory $decoratedFactory, Element $element)
    {
        $decoratedFactory->createElement('Foo')->willReturn($element);

        $this->createElement('Foo')->shouldReturn($element);
    }

    function it_delegates_create_inline_element_calls_to_the_decorated_factory(Factory $decoratedFactory, InlineElement $element)
    {
        $decoratedFactory->createInlineElement('.foo')->willReturn($element);

        $this->createInlineElement('.foo')->shouldReturn($element);
    }

    function it_creates_a_proxy_instead_of_instantiating_a_page_object_right_away(AbstractLazyFactory $proxyFactory, LazyLoadingInterface $proxy)
    {
        $proxyFactory->createProxy('Acme\Page', Argument::type('Closure'))->willReturn($proxy);

        $this->create('Acme\Page')->shouldReturn($proxy);
    }

    function it_delegates_instantiation_to_the_decorated_factory(AbstractLazyFactory $proxyFactory, LazyLoadingInterface $proxy, PageObject $pageObject, Factory $decoratedFactory)
    {
        $decoratedFactory->create('Acme\Page')->willReturn($pageObject);

        $proxyFactory->createProxy(
            Argument::any(),
            Argument::that(function ($callback) use ($proxy, $pageObject) {
                $wrappedObject = new \stdClass();
                $initializer = function () {};

                $result = $callback($wrappedObject, $proxy->getWrappedObject(), null, array(), $initializer);

                if ($wrappedObject !== $pageObject->getWrappedObject()) {
                    throw new \LogicException('Expected the proxy to be overwritten with the actual object');
                }

                if (null !== $initializer) {
                    throw new \LogicException('Expected the initializer callback to be set to null');
                }

                return $result;
            })
        )->shouldBeCalled();

        $this->create('Acme\Page');
    }
}
