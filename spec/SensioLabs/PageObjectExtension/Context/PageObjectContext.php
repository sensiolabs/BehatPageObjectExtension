<?php

namespace spec\SensioLabs\PageObjectExtension\Context;

use PHPSpec2\ObjectBehavior;

class PageObjectContext extends ObjectBehavior
{
    function it_should_be_a_behat_context()
    {
        $this->shouldHaveType('Behat\Behat\Context\ExtendedContextInterface');
    }

    /**
     * @param \SensioLabs\PageObjectExtension\Context\PageFactory   $pageFactory
     * @param \SensioLabs\PageObjectExtension\PageObject\PageObject $page
     */
    function it_should_create_a_page($pageFactory, $page)
    {
        $pageFactory->create('Article list')->willReturn($page);

        $this->setPageFactory($pageFactory);
        $this->getPage('Article list')->shouldReturn($page);
    }
}
