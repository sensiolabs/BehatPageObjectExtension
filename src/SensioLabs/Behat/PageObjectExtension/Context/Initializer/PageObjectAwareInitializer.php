<?php

namespace SensioLabs\Behat\PageObjectExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use SensioLabs\Behat\PageObjectExtension\Context\PageObjectAwareInterface;
use SensioLabs\Behat\PageObjectExtension\Context\PageFactory;

class PageObjectAwareInitializer implements ContextInitializer
{
    /**
     * @var PageFactory $pageFactory
     */
    private $pageFactory = null;

    /**
     * @param PageFactory $pageFactory
     */
    public function __construct(PageFactory $pageFactory)
    {
        $this->pageFactory = $pageFactory;
    }

    /**
     * Supports the  provided context?
     *
     * @param Context $context
     *
     * @return boolean
     */
    public function supports(Context $context)
    {
        return $context instanceof PageObjectAwareInterface;
    }

    /**
     * Initializes provided context.
     *
     * @param Context $context
     */
    public function initializeContext(Context $context)
    {
        if (!$this->supports($context)) {
            return;
        }

        $context->setPageFactory($this->pageFactory);
    }
}
