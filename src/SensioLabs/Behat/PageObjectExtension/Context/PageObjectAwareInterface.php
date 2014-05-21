<?php

namespace SensioLabs\Behat\PageObjectExtension\Context;

interface PageObjectAwareInterface
{
    /**
     * @param PageFactoryInterface $pageFactory
     */
    public function setPageFactory(PageFactoryInterface $factory);
}
