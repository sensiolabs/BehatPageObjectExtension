<?php

namespace SensioLabs\Behat\PageObjectExtension\Context;

use Behat\Behat\Context\BehatContext;
use SensioLabs\Behat\PageObjectExtension\Context\PageFactory;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;

class PageObjectContext extends BehatContext implements PageObjectAwareInterface
{
    /**
     * @var PageFactory $pageFactory
     */
    private $pageFactory = null;

    /**
     * @param string $name
     *
     * @return Page
     *
     * @throws \RuntimeException
     */
    public function getPage($name)
    {
        if (null === $this->pageFactory) {
            throw new \RuntimeException('To create pages you need to pass a factory with setPageFactory()');
        }

        return $this->pageFactory->createPage($name);
    }

    /**
     * @param string $name
     *
     * @return Element
     *
     * @throws \RuntimeException
     */
    public function getElement($name)
    {
        if (null === $this->pageFactory) {
            throw new \RuntimeException('To create elements you need to pass a factory with setPageFactory()');
        }

        return $this->pageFactory->createElement($name);
    }

    /**
     * @param PageFactory $pageFactory
     */
    public function setPageFactory(PageFactory $pageFactory)
    {
        $this->pageFactory = $pageFactory;
    }

    /**
     * @return PageFactory
     */
    public function getPageFactory()
    {
        if (null === $this->pageFactory) {
            throw new \RuntimeException('To access the page factory you need to pass it first with setPageFactory()');
        }

        return $this->pageFactory;
    }
}
