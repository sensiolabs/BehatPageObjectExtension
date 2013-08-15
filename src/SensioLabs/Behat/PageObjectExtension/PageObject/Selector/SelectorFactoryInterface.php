<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject\Selector;

interface SelectorFactoryInterface
{
    /**
     * Create Selector from given parameters
     *
     * @return SelectorInterface
     */
    public function create();
}
