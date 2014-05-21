<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use SensioLabs\Behat\PageObjectExtension\Context\PageFactory;

class PageObjectAwareInitializerSpec extends ObjectBehavior
{
    function let(PageFactory $pageFactory)
    {
        $this->beConstructedWith($pageFactory);
    }

    function it_should_be_an_initializer()
    {
        $this->shouldHaveType('Behat\Behat\Context\Initializer\ContextInitializer');
    }

    function it_should_inject_the_page_factory_into_the_context(Context $context, PageFactory $pageFactory)
    {
        $context->implement('SensioLabs\Behat\PageObjectExtension\Context\PageObjectAware');

        $context->setPageFactory($pageFactory)->shouldBeCalled();

        $this->initializeContext($context)->shouldReturn(null);
    }

    function it_should_not_inject_the_page_factory_into_non_page_object_aware_contexts(Context $context)
    {
        $this->initializeContext($context)->shouldReturn(null);
    }
}
