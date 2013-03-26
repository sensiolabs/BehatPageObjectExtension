<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\Context;

use PHPSpec2\ObjectBehavior;

class PageObjectContext extends ObjectBehavior
{
    function it_should_be_a_behat_context()
    {
        $this->shouldHaveType('Behat\Behat\Context\ExtendedContextInterface');
    }

    /**
     * @param \SensioLabs\Behat\PageObjectExtension\Context\PageFactory $pageFactory
     * @param \SensioLabs\Behat\PageObjectExtension\PageObject\Page     $page
     */
    function it_should_create_a_page($pageFactory, $page)
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
