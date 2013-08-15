<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject\Selector;

interface SelectorInterface
{
    /**
     * Return type of selector
     *
     * @return string
     */
    public function getType();

    /**
     * Return path for selector
     *
     * @return string
     */
    public function getPath();

    /**
     * Return array representation of selector used by PageFactory::createInlineElement
     *
     * @return array
     */
    public function asArray();
}
