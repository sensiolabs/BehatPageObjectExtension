<?php

namespace SensioLabs\PageObjectExtension\Context;

use SensioLabs\PageObjectExtension\Context\PageFactory;
use SensioLabs\PageObjectExtension\PageObject\PageObject;

class PageObjectContext implements PageObjectAwareInterface
{
    /**
     * @var PageFactory $pageFactory
     */
    private $pageFactory = null;

    /**
     * @param string $page
     *
     * @return PageObject
     */
    public function getPage($page)
    {
        return $this->pageFactory->create($page);
    }

    /**
     * @param PageFactory $pageFactory
     */
    public function setPageFactory(PageFactory $pageFactory)
    {
        $this->pageFactory = $pageFactory;
    }
}
