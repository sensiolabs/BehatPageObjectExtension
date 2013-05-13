<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\Context\Initializer;

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
        $this->shouldHaveType('Behat\Behat\Context\Initializer\InitializerInterface');
    }

    function it_supports_page_object_aware($context)
    {
        $context->implement('SensioLabs\Behat\PageObjectExtension\Context\PageObjectAwareInterface');
        $context->implement('Behat\Behat\Context\ContextInterface');

        $this->supports($context)->shouldReturn(true);
    }

    function it_should_not_support_other_contexts($context)
    {
        $context->implement('Behat\Behat\Context\ContextInterface');

        $this->supports($context)->shouldReturn(false);
    }

    function it_should_inject_the_page_factory_into_the_context($context, $pageFactory)
    {
        $context->implement('SensioLabs\Behat\PageObjectExtension\Context\PageObjectAwareInterface');
        $context->implement('Behat\Behat\Context\ContextInterface');

        $context->setPageFactory($pageFactory)->shouldBeCalled();

        $this->initialize($context)->shouldReturn(null);
    }
}
