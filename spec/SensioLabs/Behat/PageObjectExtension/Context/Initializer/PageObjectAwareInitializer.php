<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\Context\Initializer;

use PHPSpec2\ObjectBehavior;

class PageObjectAwareInitializer extends ObjectBehavior
{
    /**
     * @param \SensioLabs\Behat\PageObjectExtension\Context\PageFactory $pageFactory
     */
    function let($pageFactory)
    {
        $this->beConstructedWith($pageFactory);
    }

    function it_should_be_an_initializer()
    {
        $this->shouldHaveType('Behat\Behat\Context\Initializer\InitializerInterface');
    }

    /**
     * @param \SensioLabs\Behat\PageObjectExtension\Context\PageObjectAwareInterface,\Behat\Behat\Context\ContextInterface $context
     */
    function it_supports_page_object_aware($context)
    {
        $this->supports($context)->shouldReturn(true);
    }

    /**
     * @param \Behat\Behat\Context\ContextInterface $context
     */
    function it_should_not_support_other_contexts($context)
    {
        $this->supports($context)->shouldReturn(false);
    }

    /**
     * @param \SensioLabs\Behat\PageObjectExtension\Context\PageObjectAwareInterface,\Behat\Behat\Context\ContextInterface $context
     */
    function it_should_inject_the_page_factory_into_the_context($context, $pageFactory)
    {
        $context->setPageFactory($pageFactory)->shouldBeCalled();

        $this->initialize($context)->shouldReturn(null);
    }
}
