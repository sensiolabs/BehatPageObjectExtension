<?php

namespace SensioLabs\Behat\PageObjectExtension\Context\Initializer;

use Behat\Behat\Context\ContextInterface;
use Behat\Behat\Context\Initializer\InitializerInterface;
use SensioLabs\Behat\PageObjectExtension\Context\PageObjectAwareInterface;
use SensioLabs\Behat\PageObjectExtension\Context\PageFactory;

class PageObjectAwareInitializer implements InitializerInterface
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
     * @param ContextInterface $context
     *
     * @return boolean
     */
    public function supports(ContextInterface $context)
    {
        return $context instanceof PageObjectAwareInterface;
    }

    /**
     * @param ContextInterface $context
     */
    public function initialize(ContextInterface $context)
    {
        $context->setPageFactory($this->pageFactory);
    }
}
