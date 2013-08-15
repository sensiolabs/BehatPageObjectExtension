<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\Context;

use Behat\Mink\Mink;
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session;
use PhpSpec\ObjectBehavior;
use SensioLabs\Behat\PageObjectExtension\PageObject\Selector\SelectorFactory;
use SensioLabs\Behat\PageObjectExtension\PageObject\Selector\SelectorFactoryInterface;
use SensioLabs\Behat\PageObjectExtension\PageObject\Selector\SelectorInterface;

require_once __DIR__.'/Fixtures/ArticleList.php';
require_once __DIR__.'/Fixtures/NamespacedArticleList.php';
require_once __DIR__.'/Fixtures/Element/SearchBox.php';
require_once __DIR__.'/Fixtures/Element/NamespacedSearchBox.php';

class PageFactorySpec extends ObjectBehavior
{
    function let(Mink $mink, Session $session, SelectorFactoryInterface $selectorFactory, SelectorsHandler $selectorsHandler, SelectorInterface $selector1, SelectorInterface $selector2)
    {
        $this->beConstructedWith($mink, $selectorFactory, array('base_url' => 'http://behat.dev'));

        $selectorFactory->create(array('xpath' => '//div[@id="search"]'))->willReturn(
            $this->create_selector($selector1, 'xpath', '//div[@id="search"]')
        );

        $selectorFactory->create(array('xpath' => '//'))->willReturn(
            $this->create_selector($selector2, 'xpath', '//')
        );

        $mink->getSession()->willReturn($session);
        $mink->__destruct()->willReturn();

        $session->getSelectorsHandler()->willReturn($selectorsHandler);



        $selectorsHandler->selectorToXpath('xpath', '//div[@id="search"]')->willReturn('//div[@id="search"]');
        $selectorsHandler->selectorToXpath('xpath', '//')->willReturn('//');
    }

    function it_should_create_a_page()
    {
        $this->createPage('Article list')->shouldBeAnInstanceOf('ArticleList');
    }

    function it_should_create_an_element()
    {
        $this->createElement('Search box')->shouldBeAnInstanceOf('SearchBox');
    }

    function it_should_create_an_inline_element(SelectorFactoryInterface $selectorFactory)
    {
        $element = $this->createInlineElement(array('xpath' => '//div[@id="search"]'), $selectorFactory);
        $element->shouldBeAnInstanceOf('SensioLabs\Behat\PageObjectExtension\PageObject\InlineElement');
        $element->getXPath()->shouldReturn('//div[@id="search"]');
    }

    function it_should_overwrite_the_default_page_namespace()
    {
        foreach ($this->getPageNamespaces() as $namespace => $class) {
            $this->setPageNamespace($namespace);
            $this->createPage('Namespaced Article list')->shouldBeAnInstanceOf($class);
        }
    }

    function it_converts_object_selector_into_array(SelectorInterface $selector)
    {
        $selector->asArray()->willReturn(array('xpath' => '//div[@id="search"]'));
        $selector->asArray()->shouldBeCalled();

        $this->createInlineElement($selector);
    }

    private function getPageNamespaces()
    {
        return array(
            'spec\SensioLabs\Behat\PageObjectExtension\Context\Fixtures' => '\spec\SensioLabs\Behat\PageObjectExtension\Context\Fixtures\NamespacedArticleList',
            '\spec\SensioLabs\Behat\PageObjectExtension\Context\Fixtures' => '\spec\SensioLabs\Behat\PageObjectExtension\Context\Fixtures\NamespacedArticleList',
            'spec\SensioLabs\Behat\PageObjectExtension\Context\Fixtures\\' => '\spec\SensioLabs\Behat\PageObjectExtension\Context\Fixtures\NamespacedArticleList'
        );
    }

    function it_should_overwrite_the_default_element_namespace()
    {
        foreach ($this->getElementNamespaces() as $namespace => $class) {
            $this->setElementNamespace($namespace);
            $this->createElement('Namespaced search box')->shouldBeAnInstanceOf($class);
        }
    }

    private function getElementNamespaces()
    {
        return array(
            'spec\SensioLabs\Behat\PageObjectExtension\Context\Fixtures\Element' => '\spec\SensioLabs\Behat\PageObjectExtension\Context\Fixtures\Element\NamespacedSearchBox',
            '\spec\SensioLabs\Behat\PageObjectExtension\Context\Fixtures\Element' => '\spec\SensioLabs\Behat\PageObjectExtension\Context\Fixtures\Element\NamespacedSearchBox',
            'spec\SensioLabs\Behat\PageObjectExtension\Context\Fixtures\Element\\' => '\spec\SensioLabs\Behat\PageObjectExtension\Context\Fixtures\Element\NamespacedSearchBox'
        );
    }

    function it_should_complain_if_page_does_not_exist()
    {
        $this->shouldThrow(new \LogicException('"Home" page not recognised. "\\Home" class not found.'))->duringCreatePage('Home');
    }

    function it_should_complain_if_element_does_not_exist()
    {
        $this->shouldThrow(new \LogicException('"Navigation" element not recognised. "\\Navigation" class not found.'))->duringCreateElement('Navigation');
    }

    private function create_selector(SelectorInterface $selector, $type, $path)
    {
        $selector->getType()->willReturn($type);
        $selector->getPath()->willReturn($path);
        $selector->asArray()->willReturn(array($type => $path));

        return $selector;
    }
}
