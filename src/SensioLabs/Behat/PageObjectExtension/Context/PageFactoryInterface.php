<?php

namespace SensioLabs\Behat\PageObjectExtension\Context;

use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\InlineElement;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

interface PageFactoryInterface
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
     * @param string|array $selector
     *
     * @return InlineElement
     */
    public function createInlineElement($selector);

    /**
     * @param string $namespace
     */
    public function setPageNamespace($namespace);

    /**
     * @param string $namespace
     */
    public function setElementNamespace($namespace);
}
