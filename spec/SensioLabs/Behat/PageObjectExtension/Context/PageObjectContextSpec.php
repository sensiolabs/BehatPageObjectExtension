<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\Context;

use PhpSpec\ObjectBehavior;
use SensioLabs\Behat\PageObjectExtension\Context\PageFactory;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

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

    function it_should_complain_if_page_factory_was_not_set()
    {
        $this->shouldThrow(new \RuntimeException('To create pages you need to pass a factory with setPageFactory()'))
            ->duringGetPage('Article list');
    }
}
