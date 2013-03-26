<?php

namespace SensioLabs\Behat\PageObjectExtension\Context;

interface PageObjectAwareInterface
{
    /**
     * @param PageFactory $pageFactory
     */
    public function setPageFactory(PageFactory $factory);
}
