<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory as PageObjectFactory;

class PageObjectAwareInitializerSpec extends ObjectBehavior
{
    function let(PageObjectFactory $pageObjectFactory)
    {
        $this->beConstructedWith($pageObjectFactory);
    }

    function it_should_be_an_initializer()
    {
        $this->shouldHaveType('Behat\Behat\Context\Initializer\ContextInitializer');
    }

    function it_should_inject_the_page_factory_into_the_context(Context $context, PageObjectFactory $pageObjectFactory)
    {
        $context->implement('SensioLabs\Behat\PageObjectExtension\Context\PageObjectAware');

        $context->setPageObjectFactory($pageObjectFactory)->shouldBeCalled();

        $this->initializeContext($context)->shouldReturn(null);
    }

    function it_should_not_inject_the_page_factory_into_non_page_object_aware_contexts(Context $context)
    {
        $this->initializeContext($context)->shouldReturn(null);
    }
}
