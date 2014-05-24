<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject;

interface Factory
{
    /**
     * @param string
     *
     * @return Page
     */
    public function createPage($name);

    /**
     * @param string
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
}
