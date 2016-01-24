<?php

namespace SensioLabs\Behat\PageObjectExtension\Context;

@trigger_error('The '.__NAMESPACE__.'\PageObjectContext class is deprecated since version 2.0 and will be removed in 3.0. Use the argument injection instead.', E_USER_DEPRECATED);

use Behat\Behat\Context\Context;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory as PageObjectFactory;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;

/**
 * @deprecated in 2.0, to be removed in 3.0. Use the argument resolver instead. See http://behat-page-object-extension.readthedocs.org/en/latest/guide/working_with_page_objects.html#instantiating-a-page-object
 */
class PageObjectContext implements Context, PageObjectAware
{
    /**
     * @var PageObjectFactory
     */
    private $pageObjectFactory = null;

    /**
     * @param string $name
     *
     * @return Page
     *
     * @throws \RuntimeException
     */
    public function getPage($name)
    {
        if (null === $this->pageObjectFactory) {
            throw new \RuntimeException('To create pages you need to pass a factory with setPageObjectFactory()');
        }

        return $this->pageObjectFactory->createPage($name);
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
        if (null === $this->pageObjectFactory) {
            throw new \RuntimeException('To create elements you need to pass a factory with setPageObjectFactory()');
        }

        return $this->pageObjectFactory->createElement($name);
    }

    /**
     * @param PageObjectFactory $pageObjectFactory
     */
    public function setPageObjectFactory(PageObjectFactory $pageObjectFactory)
    {
        $this->pageObjectFactory = $pageObjectFactory;
    }

    /**
     * @return PageObjectFactory
     */
    public function getPageObjectFactory()
    {
        if (null === $this->pageObjectFactory) {
            throw new \RuntimeException('To access the page factory you need to pass it first with setPageObjectFactory()');
        }

        return $this->pageObjectFactory;
    }
}
