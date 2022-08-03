<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject;

interface Factory
{
    /**
     * @template T of Page
     *
     * @param class-string<T> $name
     *
     * @return T
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
     * @param null|string  $name
     *
     * @return InlineElement
     */
    public function createInlineElement($selector, $name = null);

    /**
     * @param string $pageObjectClass
     *
     * @return PageObject
     */
    public function create($pageObjectClass);
}
