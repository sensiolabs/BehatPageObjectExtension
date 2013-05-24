<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\Context;

use Behat\Mink\Mink;
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session;
use PhpSpec\ObjectBehavior;

require_once __DIR__.'/Fixtures/ArticleList.php';
require_once __DIR__.'/Fixtures/NamespacedArticleList.php';
require_once __DIR__.'/Fixtures/Element/SearchBox.php';
require_once __DIR__.'/Fixtures/Element/NamespacedSearchBox.php';

class PageFactorySpec extends ObjectBehavior
{
    function let(Mink $mink, Session $session, SelectorsHandler $selectorsHandler)
    {
        $this->beConstructedWith($mink, array('base_url' => 'http://behat.dev'));

        $mink->getSession()->willReturn($session);
        $mink->__destruct()->willReturn();
        $session->getSelectorsHandler()->willReturn($selectorsHandler);
        $selectorsHandler->selectorToXpath('xpath', '//div[@id="search"]')->willReturn('//div[@id="search"]');
    }

    function it_should_create_a_page()
    {
        $this->createPage('Article list')->shouldBeAnInstanceOf('ArticleList');
    }

    function it_should_create_an_element()
    {
        $this->createElement('Search box')->shouldBeAnInstanceOf('SearchBox');
    }

    function it_should_create_an_inline_element()
    {
        $element = $this->createInlineElement(array('xpath' => '//div[@id="search"]'));
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
}
