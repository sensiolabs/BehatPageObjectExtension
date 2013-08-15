<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject\Selector;

use SensioLabs\Behat\PageObjectExtension\PageObject\Selector;

class CssSelector extends Selector implements SelectorInterface
{
    /**
     * Return css type for selector
     *
     * @return string
     */
    public function getType()
    {
        return 'css';
    }
}
