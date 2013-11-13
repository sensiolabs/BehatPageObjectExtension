<?php

namespace SensioLabs\Behat\PageObjectExtension\Context;

use Behat\Behat\Context\BehatContext;
use SensioLabs\Behat\PageObjectExtension\Context\PageFactory;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class PageObjectContext extends BehatContext implements PageObjectAwareInterface
{
    /**
     * @var PageFactory $pageFactory
     */
    private $pageFactory = null;

    /**
     * @param string $name
     * @throws \RuntimeException
     * @return Page
     */
    public function getPage($name)
    {
        if (null === $this->pageFactory) {
            throw new \RuntimeException('To create pages you need to pass a factory with setPageFactory()');
        }

        return $this->pageFactory->createPage($name);
    }

    /**
     * @param $name
     * @throws \RuntimeException
     * @return \SensioLabs\Behat\PageObjectExtension\PageObject\Element
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
}
