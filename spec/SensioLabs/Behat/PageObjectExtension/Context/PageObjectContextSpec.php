<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\Context;

use PhpSpec\ObjectBehavior;
use SensioLabs\Behat\PageObjectExtension\Context\PageFactory;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;

class PageObjectContextSpec extends ObjectBehavior
{
    function it_should_be_a_behat_context()
    {
        $this->shouldHaveType('Behat\Behat\Context\ExtendedContextInterface');
    }

    function it_should_create_a_page(PageFactory $pageFactory, Page $page)
    {
        $pageFactory->createPage('Article list')->willReturn($page);

        $this->setPageFactory($pageFactory);
        $this->getPage('Article list')->shouldReturn($page);
    }

    function it_should_create_an_element(PageFactory $pageFactory, Element $element)
    {
        $pageFactory->createElement('Search box')->willReturn($element);

        $this->setPageFactory($pageFactory);
        $this->getElement('Search box')->shouldReturn($element);
    }

    function it_should_complain_if_page_factory_was_not_set()
    {
        $this->shouldThrow(new \RuntimeException('To create pages you need to pass a factory with setPageFactory()'))
            ->duringGetPage('Article list');

        $this->shouldThrow(new \RuntimeException('To create elements you need to pass a factory with setPageFactory()'))
            ->duringGetElement('Search box');
    }

    function it_exposes_the_page_factory(PageFactory $pageFactory)
    {
        $this->setPageFactory($pageFactory);

        $this->getPageFactory()->shouldReturn($pageFactory);
    }

    function it_throws_an_exception_if_page_factory_is_not_set_but_accessed()
    {
        $this->shouldThrow(new \RuntimeException('To access the page factory you need to pass it first with setPageFactory()'))
            ->duringGetPageFactory();
    }
}
