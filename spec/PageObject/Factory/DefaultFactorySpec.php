<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\PageObject\Factory;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Mink;
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory\ClassNameResolver;

require_once __DIR__.'/Fixtures/ArticleList.php';
require_once __DIR__.'/Fixtures/Element/SearchBox.php';
require_once __DIR__.'/Fixtures/Element/InlineSearchBox.php';

class DefaultFactorySpec extends ObjectBehavior
{
    function let(Mink $mink, Session $session, SelectorsHandler $selectorsHandler, ClassNameResolver $classNameResolver, DriverInterface $driver)
    {
        $this->beConstructedWith($mink, $classNameResolver, array('base_url' => 'http://behat.dev'));

        $mink->getSession()->willReturn($session);
        $mink->__destruct()->willReturn();
        $session->getSelectorsHandler()->willReturn($selectorsHandler);
        $session->getDriver()->willReturn($driver);
        $selectorsHandler->selectorToXpath('xpath', '//div[@id="search"]')->willReturn('//div[@id="search"]');
        $selectorsHandler->selectorToXpath('xpath', '//div[@id="inline-search"]')->willReturn('//div[@id="inline-search"]');
    }

    function it_is_a_page_object_factory()
    {
        $this->shouldHaveType('SensioLabs\Behat\PageObjectExtension\PageObject\Factory');
    }

    function it_should_create_a_page(ClassNameResolver $classNameResolver)
    {
        $classNameResolver->resolvePage('Article list')->willReturn('ArticleList');

        $this->createPage('Article list')->shouldBeAnInstanceOf('ArticleList');
    }

    function it_should_create_an_element(ClassNameResolver $classNameResolver)
    {
        $classNameResolver->resolveElement('Search box')->willReturn('SearchBox');

        $this->createElement('Search box')->shouldBeAnInstanceOf('SearchBox');
    }

    function it_should_create_an_inline_element(ClassNameResolver $classNameResolver)
    {
        $classNameResolver->resolveElement('SensioLabs\Behat\PageObjectExtension\PageObject\InlineElement')
            ->willReturn('SensioLabs\Behat\PageObjectExtension\PageObject\InlineElement');

        $element = $this->createInlineElement(array('xpath' => '//div[@id="search"]'));
        $element->shouldBeAnInstanceOf('SensioLabs\Behat\PageObjectExtension\PageObject\InlineElement');
        $element->getXPath()->shouldReturn('//div[@id="search"]');
    }

    function it_create_inline_element_should_throw_an_exception_with_non_inline_element_class(ClassNameResolver $classNameResolver)
    {
        $classNameResolver->resolveElement(Argument::any())->willReturn('ArticleList');

        $this->shouldThrow('InvalidArgumentException')->duringCreateInlineElement(array('xpath' => '//div[@id="search"]'), 'ArticleList');
    }

    function it_should_create_a_named_inline_element(ClassNameResolver $classNameResolver)
    {
        $classNameResolver->resolveElement('Inline Search Box')->willReturn('InlineSearchBox');

        $element = $this->createInlineElement(array('xpath' => '//div[@id="inline-search"]'), 'Inline Search Box');
        $element->shouldBeAnInstanceOf('InlineSearchBox');
        $element->getXPath()->shouldReturn('//div[@id="inline-search"]');
    }

    function it_creates_a_page()
    {
        $this->create('ArticleList')->shouldBeAnInstanceOf('ArticleList');
    }

    function it_creates_an_element()
    {
        $this->create('SearchBox')->shouldBeAnInstanceOf('SearchBox');
    }

    function it_throws_exception_for_a_non_element_and_non_page_class()
    {
        $this->shouldThrow(new \InvalidArgumentException(sprintf('Not a page object class: stdClass')))
            ->duringCreate('stdClass');
    }
}
