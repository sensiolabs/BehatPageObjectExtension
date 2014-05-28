<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\PageObject\Factory;

use Behat\Mink\Mink;
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session;
use PhpSpec\ObjectBehavior;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory\ClassNameResolver;

require_once __DIR__.'/Fixtures/ArticleList.php';
require_once __DIR__.'/Fixtures/Element/SearchBox.php';

class DefaultFactorySpec extends ObjectBehavior
{
    function let(Mink $mink, Session $session, SelectorsHandler $selectorsHandler, ClassNameResolver $classNameResolver)
    {
        $this->beConstructedWith($mink, $classNameResolver, array('base_url' => 'http://behat.dev'));

        $mink->getSession()->willReturn($session);
        $mink->__destruct()->willReturn();
        $session->getSelectorsHandler()->willReturn($selectorsHandler);
        $selectorsHandler->selectorToXpath('xpath', '//div[@id="search"]')->willReturn('//div[@id="search"]');
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

    function it_should_create_an_inline_element()
    {
        $element = $this->createInlineElement(array('xpath' => '//div[@id="search"]'));
        $element->shouldBeAnInstanceOf('SensioLabs\Behat\PageObjectExtension\PageObject\InlineElement');
        $element->getXPath()->shouldReturn('//div[@id="search"]');
    }
}
