<?php

namespace SensioLabs\Behat\PageObjectExtension\Context\Initializer;

@trigger_error('The '.__NAMESPACE__.'\PageObjectAwareInitializer class is deprecated since version 2.0 and will be removed in 3.0. Use the argument injection instead.', E_USER_DEPRECATED);

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use SensioLabs\Behat\PageObjectExtension\Context\PageObjectAware;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory as PageObjectFactory;

/**
 * @deprecated in 2.0, to be removed in 3.0. Use the argument resolver instead.
 */
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
