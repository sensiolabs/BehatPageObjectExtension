<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\PageObject\Factory;

require_once __DIR__.'/Fixtures/ArticleList.php';

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;
use SensioLabs\Behat\PageObjectExtension\PageObject\InlineElement;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use SensioLabs\Behat\PageObjectExtension\PageObject\PageObject;

class LazyFactorySpec extends ObjectBehavior
{
    function let(Factory $decoratedFactory)
    {
        $this->beConstructedWith($decoratedFactory, new LazyLoadingValueHolderFactory());
    }

    function it_is_a_page_object_factory()
    {
        $this->shouldHaveType('SensioLabs\Behat\PageObjectExtension\PageObject\Factory');
    }

    function it_delegates_create_page_calls_to_the_decorated_factory(Factory $decoratedFactory, Page $page)
    {
        $decoratedFactory->createPage('ArticleList')->willReturn($page);

        $this->createPage('ArticleList')->shouldReturn($page);
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

    function it_creates_a_proxy_instead_of_instantiating_a_page_object_right_away()
    {
        $this->create('ArticleList')->shouldReturnAnInstanceOf('ProxyManager\Proxy\LazyLoadingInterface');
    }

    function it_delegates_instantiation_to_the_decorated_factory(PageObject $pageObject, Factory $decoratedFactory)
    {
        $decoratedFactory->create('ArticleList')->willReturn($pageObject);

        $this->create('ArticleList')->shouldReturnAnInstanceOf('ProxyManager\Proxy\ProxyInterface');
    }
}
