<?php

namespace SensioLabs\Behat\PageObjectExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use SensioLabs\Behat\PageObjectExtension\Context\PageObjectAware;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory as PageObjectFactory;

class PageObjectAwareInitializer implements ContextInitializer
{
    /**
     * @var PageObjectFactory
     */
    private $pageObjectFactory = null;

    /**
     * @param PageObjectFactory $pageObjectFactory
     */
    public function __construct(PageObjectFactory $pageObjectFactory)
    {
        $this->pageObjectFactory = $pageObjectFactory;
    }

    /**
     * @param Context $context
     */
    public function initializeContext(Context $context)
    {
        if ($context instanceof PageObjectAware) {
            $context->setPageObjectFactory($this->pageObjectFactory);
        }
    }
}
