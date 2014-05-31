<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject;

interface Factory
{
    /**
     * @param string $name
     *
     * @return Page
     */
    public function createPage($name);

    /**
     * @param string $name
     *
     * @return Element
     */
    public function createElement($name);

    /**
     * @param string|array
     *
     * @return InlineElement
     */
    public function createInlineElement($selector);

    /**
     * @param string $class
     *
     * @return PageObject
     */
    public function instantiate($class);
}
