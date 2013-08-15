<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject;

use SensioLabs\Behat\PageObjectExtension\PageObject\Selector\SelectorInterface;

abstract class Selector implements SelectorInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Return path for current selector
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Return array representation of selector for PageFactory::createInlineElement
     *
     * @return array
     */
    public function asArray()
    {
        return array($this->getType() => $this->getPath());
    }
}
