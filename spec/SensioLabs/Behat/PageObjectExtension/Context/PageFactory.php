<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\Context;

use PHPSpec2\ObjectBehavior;

class PageFactory extends ObjectBehavior
{
    /**
     * @param \Behat\Mink\Session                   $session
     * @param \Behat\Mink\Selector\SelectorsHandler $selectorsHandler
     */
    function let($session, $selectorsHandler)
    {
        $this->beConstructedWith($session, array('base_url' => 'http://behat.dev'));

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
