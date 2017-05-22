<?php

namespace SensioLabs\Behat\PageObjectExtension\Context;

use SensioLabs\Behat\PageObjectExtension\PageObject\Factory as PageObjectFactory;

interface PageObjectAware
{
    /**
     * @param PageObjectFactory $pageObjectFactory
     *
     * @return null
     */
    public function setPageObjectFactory(PageObjectFactory $pageObjectFactory);
}
