<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\Context;

use PhpSpec\ObjectBehavior;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory as PageObjectFactory;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;

class PageObjectContextSpec extends ObjectBehavior
{
    function it_should_be_a_behat_context()
    {
        $this->shouldHaveType('Behat\Behat\Context\Context');
    }

    function it_should_create_a_page(PageObjectFactory $pageObjectFactory, Page $page)
    {
        $pageObjectFactory->createPage('Article list')->willReturn($page);

        $this->setPageObjectFactory($pageObjectFactory);
        $this->getPage('Article list')->shouldReturn($page);
    }

    function it_should_create_an_element(PageObjectFactory $pageObjectFactory, Element $element)
    {
        $pageObjectFactory->createElement('Search box')->willReturn($element);

        $this->setPageObjectFactory($pageObjectFactory);
        $this->getElement('Search box')->shouldReturn($element);
    }

    function it_should_complain_if_page_factory_was_not_set()
    {
        $this->shouldThrow(new \RuntimeException('To create pages you need to pass a factory with setPageObjectFactory()'))
            ->duringGetPage('Article list');

        $this->shouldThrow(new \RuntimeException('To create elements you need to pass a factory with setPageObjectFactory()'))
            ->duringGetElement('Search box');
    }

    function it_exposes_the_page_factory(PageObjectFactory $pageObjectFactory)
    {
        $this->setPageObjectFactory($pageObjectFactory);

        $this->getPageObjectFactory()->shouldReturn($pageObjectFactory);
    }

    function it_throws_an_exception_if_page_factory_is_not_set_but_accessed()
    {
        $this->shouldThrow(new \RuntimeException('To access the page factory you need to pass it first with setPageObjectFactory()'))
            ->duringGetPageObjectFactory();
    }
}
