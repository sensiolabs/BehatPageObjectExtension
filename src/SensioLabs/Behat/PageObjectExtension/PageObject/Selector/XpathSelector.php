<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject\Selector;

use SensioLabs\Behat\PageObjectExtension\PageObject\Selector;

class XpathSelector extends Selector implements SelectorInterface
{
    /**
     * Return xpath type for selector
     *
     * @return string
     */
    public function getType()
    {
        return 'xpath';
    }
}
