<?php

namespace SensioLabs\Behat\PageObjectExtension\Context;

interface PageObjectAware
{
    /**
     * @param PageFactory $pageFactory
     */
    public function setPageFactory(PageFactory $factory);
}
