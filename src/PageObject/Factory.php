<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject;

interface Factory
{
    /**
     * @template TPage of Page
     *
     * @param class-string<TPage> $name
     *
     * @return TPage
     */
    public function createPage($name);

    /**
     * @template TElement of Element
     *
     * @param class-string<TElement> $name
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
